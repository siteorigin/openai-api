<?php

namespace SiteOrigin\OpenAI;

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
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @see https://beta.openai.com/docs/api-reference/completions/create
     */
    public function complete(string $prompt = '', array $config = []): object
    {
        $config = array_merge($this->config, $config);
        $config = array_merge($config, ['prompt' => $prompt]);

        $response = $this->request('POST', sprintf('engines/%s/completions', $this->engine), [
            'headers' => ['content-type' => 'application/json'],
            'body' => json_encode($config),
        ]);

        return json_decode($response->getBody()->getContents());
    }

    /**
     * Synchronously complete multiple prompts using a Guzzle Pool of requests
     *
     * @param array|string[] $prompts
     * @param array $config
     * @todo Complete this function.
     */
    public function completeMultiple(array $prompts = [''], array $config = [])
    {
    }
}
