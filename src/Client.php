<?php

namespace SiteOrigin\OpenAI;

use GuzzleHttp\Client as GuzzleClient;
use SiteOrigin\OpenAI\FineTuned\FineTuned;

class Client
{
    const VERSION = 'v1';

    private ?string $apiKey;
    private ?string $organization;

    private array $options;

    private GuzzleClient $guzzle;

    public function __construct(string $apiKey = null, string $organization = null)
    {
        $this->apiKey = $apiKey ?: (! empty($_ENV['OPENAI_API_KEY']) ? $_ENV['OPENAI_API_KEY'] : null);
        $this->organization = $organization ?: (! empty($_ENV['OPENAI_API_ORG']) ? $_ENV['OPENAI_API_ORG'] : null);

        $headers = [
            'Authorization' => 'Bearer ' . $this->apiKey,
        ];

        if (! empty($this->organization)) {
            $headers['OpenAI-Organization'] = $this->organization;
        }

        $this->guzzle = new GuzzleClient([
            'base_uri' => sprintf('https://api.openai.com/%s/', self::VERSION),
            'headers' => $headers,
        ]);
    }

    public function guzzleClient(): GuzzleClient
    {
        return $this->guzzle;
    }

    public function answers(string $engine = Engines::CURIE, array $config = []): Answers
    {
        return new Answers($this, $engine, $config);
    }

    public function classifications(string $engine = Engines::CURIE, array $config = []): Classifications
    {
        return new Classifications($this, $engine, $config);
    }

    public function completions(string $engine = 'davinci', array $config = []): Completions
    {
        return new Completions($this, $engine, $config);
    }

    public function embeddings(string $engine = null, array $config = []): Embeddings
    {
        return new Embeddings($this, $engine, $config);
    }

    public function engines(): Engines
    {
        return new Engines($this);
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

    public function search(string $engine = Engines::ADA, array $config = []): Search
    {
        return new Search($this, $engine, $config);
    }

    public function fineTuned(string $model, array $config = []): FineTuned
    {
        return new FineTuned($this, $model, $config);
    }
}
