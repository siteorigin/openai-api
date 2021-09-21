<?php

namespace SiteOrigin\OpenAI;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Response;

class Completions extends Request
{
    private string $engine;

    private array $config = [];

    private $concurrency = 3;

    public function __construct(Client $client, string $engine = 'davinci', array $config = [])
    {
        parent::__construct($client);
        $this->engine = $engine;
        $this->client = $client;
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
     * Chunk prompts into multiple requests that are performed in parallel
     *
     * @param string[][] $prompts
     * @param array $config
     * @return array
     */
    public function completeMultiple(array $prompts, array $config = [], bool $returnChoices = false): array
    {
        $requests = function ($prompts) use ($config) {
            // We need to return the prompt to keep track of the multiple requests
            $config = array_merge($this->config, $config);

            foreach ($prompts as $prompt) {
                yield fn () => $this->requestAsync(
                    "POST",
                    sprintf('engines/%s/completions', $this->engine),
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

        $return = [];
        $pool = new Pool($this->client->guzzleClient(), $requests($prompts), [
            'concurrency' => $this->concurrency,
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

        if ($returnChoices) {
            foreach ($return as $r) {
                if (is_a($r, RequestException::class)) {
                    // We need to handle request exceptions if we're returning choices
                    throw $r;
                }
            }

            $return = array_map(fn ($completion) => $completion->choices, $return);
            $return = array_merge(...$return);
        }

        return $return;
    }
}
