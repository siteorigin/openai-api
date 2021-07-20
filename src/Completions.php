<?php

namespace SiteOrigin\OpenAI;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Response;

class Completions extends Request
{
    private string $engine;

    private array $config = [];

    public function __construct(Client $client, string $engine = 'davinci', array $config = [])
    {
        parent::__construct($client);
        $this->engine = $engine;
        $this->client = $client;
        $this->config = array_merge($this->config, $config);
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
     * @param string $prompt The prompt string
     * @param array $config Any additional config
     *
     * @see https://beta.openai.com/docs/api-reference/completions/create
     */
    public function complete(string $prompt = '', array $config = []): object
    {
        $config = array_merge($this->config, $config);
        $config = array_merge($config, ['prompt' => $prompt]);

        $response = $this->request(
            'POST',
            $this->engine ? sprintf('engines/%s/completions', $this->engine) : 'completions', [
                'headers' => ['content-type' => 'application/json'],
                'body' => json_encode($config),
            ]
        );

        return json_decode($response->getBody()->getContents());
    }

    /**
     * Synchronously complete multiple prompts using a Guzzle Pool of requests
     *
     * @param array|string[] $prompts
     * @param array $config
     * @param int $concurrency
     * @return array
     */
    public function completeMultiple(array $prompts = [], array $config = [], int $concurrency = 5): array
    {
        $requests = function ($prompts) use ($config) {
            // We need to return the prompt to keep track of the multiple requests
            $config = array_merge($this->config, $config);

            foreach ($prompts as $prompt) {
                $config['prompt'] = $prompt;
                yield fn () => $this->requestAsync(
                    "POST",
                    sprintf('engines/%s/completions', $this->engine),
                    [
                        'headers' => ['content-type' => 'application/json'],
                        'body' => json_encode($config),
                    ]
                );
            }
        };

        $return = [];
        $pool = new Pool($this->client->guzzleClient(), $requests($prompts), [
            'concurrency' => 5,
            'fulfilled' => function (Response $response, $index) use (&$return) {
                $return[$index] = json_decode($response->getBody()->getContents());
            },
            'rejected' => function (RequestException $reason, $index) use (&$return) {
                $return[$index] = $reason;
            },
        ]);

        // Wait for all the requests.
        $pool->promise()->wait();
        ksort($return);

        return $return;
    }
}
