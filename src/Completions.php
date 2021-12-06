<?php

namespace SiteOrigin\OpenAI;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Response;

class Completions extends Request
{
    const MAX_PER_REQUEST = 20;

    private string $engine;

    protected array $config = [];

    protected int $concurrency = 2;

    public function __construct(Client $client, string $engine = 'davinci', array $config = [])
    {
        parent::__construct($client);
        $this->engine = $engine;
        $this->config = array_merge($this->config, $config);

        if (isset($this->config['concurrency'])) {
            // Concurrency is used by this wrapper, not OpenAI
            $this->concurrency = (int) $this->config['concurrency'];
            unset($this->config['concurrency']);
        }
    }

    /**
     * @param string $engine The engine to use for this completion.
     * @return $this
     */
    public function setEngine(string $engine): static
    {
        $this->engine = $engine;

        return $this;
    }

    /**
     * Complete the given text.
     *
     * @param string|array $prompt The prompt string
     * @param array $config Any additional config
     *
     * @return object
     * @see https://beta.openai.com/docs/api-reference/completions/create
     */
    public function complete(string|array $prompt = '', array $config = []): object
    {
        if (is_array($prompt) && count($prompt) > self::MAX_PER_REQUEST) {
            return $this->completeConcurrent($prompt, $config);
        }

        $config = array_merge($this->config, $config);
        $config = array_merge($config, ['prompt' => is_array($prompt) ? array_values($prompt) : $prompt]);

        $response = $this->request(
            'POST',
            $this->engine ? sprintf('engines/%s/completions', $this->engine) : 'completions',
            [
                'headers' => ['content-type' => 'application/json'],
                'body' => json_encode($config),
            ]
        );

        return json_decode($response->getBody()->getContents());
    }

    /**
     * Chunk prompts into multiple requests that are performed concurrently
     *
     * @param string[] $prompts
     * @param array $config
     * @return object
     */
    public function completeConcurrent(array $prompts, array $config = []): object
    {
        $prompts = array_chunk($prompts, self::MAX_PER_REQUEST);

        $requests = function ($prompts) use ($config) {
            // We need to return the prompt to keep track of the multiple requests
            $config = array_merge($this->config, $config);

            foreach ($prompts as $prompt) {
                yield fn () => $this->requestAsync(
                    "POST",
                    $this->engine ? sprintf('engines/%s/completions', $this->engine) : 'completions',
                    [
                        'headers' => ['content-type' => 'application/json'],
                        'body' => json_encode(array_merge(
                            $config,
                            ['prompt' => is_array($prompt) ? array_values($prompt) : $prompt]
                        )),
                    ]
                );
            }
        };

        $responses = [];
        $pool = new Pool($this->client->guzzleClient(), $requests($prompts), [
            'concurrency' => $this->concurrency,
            'fulfilled' => function (Response $response, $index) use (&$responses) {
                $responses[$index] = json_decode($response->getBody()->getContents());
            },
            'rejected' => function (RequestException $reason) use (&$responses) {
                throw $reason;
            },
        ]);

        // Wait for all the requests.
        $pool->promise()->wait();
        ksort($responses);

        $return = array_map(fn ($completion) => $completion->choices, $responses);

        return (object) [
            'choices' => array_merge(...$return),
        ];
    }
}
