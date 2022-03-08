<?php

namespace SiteOrigin\OpenAI;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Response;

/**
 * @todo Consolidate this with the completion class
 */
class Embeddings extends Request
{
    const MAX_PER_REQUEST = 20;

    private string $engine;

    private array $config = [];

    protected int $concurrency = 2;

    public function __construct(Client $client, string $engine = null, array $config = [])
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
     * @throws \Exception
     */
    public function embed(string | array $text, ?string $engine = null, array $config  = []): object
    {
        if (is_array($text) && count($text) > self::MAX_PER_REQUEST) {
            return $this->embedConcurrent($text);
        }

        $engine = $engine ?? $this->engine;

        if (empty($engine)) {
            throw new \InvalidArgumentException('No engine specified');
        }
        if (empty($text)) {
            throw new \InvalidArgumentException('No embed text specified');
        }

        // Make sure the text is a string
        $input = is_array($text) ? array_values($text) : [$text];
        $input = array_map(fn ($t) => (string) $t, $input);

        $config = array_merge($this->config, $config);
        $response = $this->request('POST', sprintf('engines/%s/embeddings', $engine), [
            'json' => array_merge($config, [
                'input' => $input,
            ]),
        ]);

        return json_decode($response->getBody()->getContents());
    }

    /**
     * Perform the embedding request in parallel.
     *
     * @param array $text
     * @param string|null $engine
     * @return object
     */
    public function embedConcurrent(array $text, string $engine = null): object
    {
        $engine = $engine ?? $this->engine;

        $requests = function ($texts) use ($engine) {
            // We need to return the prompt to keep track of the multiple requests
            foreach ($texts as $text) {
                yield fn () => $this->requestAsync(
                    "POST",
                    sprintf('engines/%s/embeddings', $engine),
                    [
                        'headers' => ['content-type' => 'application/json'],
                        'json' => [
                            'input' => is_array($text) ? array_values($text) : [$text],
                        ],
                    ]
                );
            }
        };

        $responses = [];
        $text = array_chunk($text, self::MAX_PER_REQUEST);
        $pool = new Pool($this->client->guzzleClient(), $requests($text), [
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

        $return = array_map(fn ($completion) => $completion->data, $responses);

        return (object) [
            'data' => array_merge(...$return),
        ];
    }
}
