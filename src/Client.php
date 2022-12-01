<?php

namespace SiteOrigin\OpenAI;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use GuzzleRetry\GuzzleRetryMiddleware;
use SiteOrigin\OpenAI\FineTuned\FineTuned;

class Client
{
    const VERSION = 'v1';

    private ?string $apiKey;
    private ?string $organization;

    private array $options;

    private GuzzleClient $guzzle;

    /**
     * @param string $apiKey
     * @param string|null $organization
     * @param array|null $retries A retries config array for GuzzleRetryMiddleware. See https://github.com/caseyamcl/guzzle_retry_middleware
     */
    public function __construct(string $apiKey, string $organization = null, ?array $retries = null)
    {
        $this->apiKey = $apiKey ?: (! empty($_ENV['OPENAI_API_KEY']) ? $_ENV['OPENAI_API_KEY'] : null);
        $this->organization = $organization ?: (! empty($_ENV['OPENAI_API_ORG']) ? $_ENV['OPENAI_API_ORG'] : null);

        $headers = [
            'Authorization' => 'Bearer ' . $this->apiKey,
        ];

        if (! empty($this->organization)) {
            $headers['OpenAI-Organization'] = $this->organization;
        }

        $stack = HandlerStack::create();
        if(!is_null($retries)) {
            $stack->push(GuzzleRetryMiddleware::factory($retries));
        }

        $this->guzzle = new GuzzleClient([
            'base_uri' => sprintf('https://api.openai.com/%s/', self::VERSION),
            'headers' => $headers,
            'handler' => $stack,
        ]);
    }

    public function guzzleClient(): GuzzleClient
    {
        return $this->guzzle;
    }

    public function completions(string $engine = Models::TEXT_DAVINCI, array $config = []): Completions
    {
        return new Completions($this, $engine, $config);
    }

    public function edits(string $engine = Models::TEXT_DAVINCI_EDIT, array $config = []): Edits
    {
        return new Edits($this, $engine, $config);
    }

    public function embeddings(string $engine = null, array $config = []): Embeddings
    {
        return new Embeddings($this, $engine, $config);
    }

    public function engines(): Models
    {
        return new Models($this);
    }

    public function files(): Files
    {
        return new Files($this);
    }

    public function filter(float $toxicThreshold = Filter::TOXIC_THRESHOLD, array $config = []): Filter
    {
        return new Filter($this, $toxicThreshold, $config);
    }

    public function fineTunes(): FineTunes
    {
        return new FineTunes($this);
    }

    public function fineTuned(string $model, array $config = []): FineTuned
    {
        return new FineTuned($this, $model, $config);
    }
}
