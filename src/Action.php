<?php

namespace SiteOrigin\OpenAI;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Response;

class Action extends Request
{
    const MAX_PER_REQUEST = 20;
    const MAX_PER_REQUEST_CHAT = 1;

    protected array $config = [];

    protected int $concurrency = 2;

    private string $dataKey;
    private string $endpoint;
    private string $inputKey;

    public function __construct(Client $client, string $endpoint, string $inputKey, string $dataKey, string $model, array $config = [])
    {
        parent::__construct($client);
        $this->config = array_merge($this->config, $config, ['model' => $model]);

        if (isset($this->config['concurrency'])) {
            // Concurrency is used by this wrapper, not OpenAI
            $this->concurrency = (int) $this->config['concurrency'];
            unset($this->config['concurrency']);
        }
        $this->dataKey = $dataKey;
        $this->endpoint = $endpoint;
        $this->inputKey = $inputKey;
    }

    /**
     * @param string $model The engine to use for this completion.
     * @return $this
     */
    public function setModel(string $model): static
    {
        $this->config = array_merge($this->config, ['model' => $model]);

        return $this;
    }

    /**
     * Complete the given text.
     *
     * @param string|array $input The input string or strings
     * @param array $config Any additional config
     *
     * @return object
     * @see https://beta.openai.com/docs/api-reference/completions/create
     */
    protected function action(string | array $input = '', array $config = []): object
    {
        // If we're using the chat endpoint
        if (is_array($input) && count($input) > self::MAX_PER_REQUEST && $this->endpoint !== 'chat/completions') {
            return $this->actionConcurrent($input, $config);
        }

        $config = array_merge($this->config, $config);
        $config = array_merge($config, [$this->inputKey => is_array($input) ? array_values($input) : $input]);

        $response = $this->request(
            'POST',
            $this->endpoint,
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
     * @param string[] $inputs
     * @param array $config
     * @return object
     */
    protected function actionConcurrent(array $inputs, array $config = []): object
    {
        if ($this->endpoint !== 'chat/completions') {
            $inputs = array_chunk($inputs, self::MAX_PER_REQUEST);
        }

        $requests = function ($prompts) use ($config) {
            // We need to return the prompt to keep track of the multiple requests
            $config = array_merge($this->config, $config);

            foreach ($prompts as $prompt) {
                yield fn () => $this->requestAsync(
                    "POST",
                    $this->endpoint,
                    [
                        'headers' => ['content-type' => 'application/json'],
                        'body' => json_encode(array_merge(
                            $config,
                            [$this->inputKey => is_array($prompt) ? array_values($prompt) : $prompt]
                        )),
                    ]
                );
            }
        };

        $responses = [];
        $pool = new Pool($this->client->guzzleClient(), $requests($inputs), [
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

        $return = array_map(fn ($completion) => $completion->{$this->dataKey}, $responses);

        return (object) [
            $this->dataKey => array_merge(...$return),
        ];
    }
}
