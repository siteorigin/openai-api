<?php

namespace SiteOrigin\OpenAI;

class Classifications extends Request
{
    private string $engine;

    private array $config;

    public function __construct(Client $client, string $engine = Engines::CURIE, array $config = [])
    {
        parent::__construct($client);
        $this->engine = $engine;
        $this->config = $config;
    }

    /**
     * @param string $engine The engine we'll use for the next classification.
     * @return $this
     */
    public function setEngine(string $engine): static
    {
        $this->engine = $engine;

        return $this;
    }

    /**
     * Perform a classify operation.
     *
     * @param string $query The string we want to classify.
     * @param string|array $examples A string file ID, or an array of examples.
     * @param array $config Configuration for the request.
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @see https://beta.openai.com/docs/api-reference/classifications
     */
    public function create(string $query, string | array $examples, array $config = []): object
    {
        $config = array_merge($this->config, $config);
        $config = array_merge($config, [
            'model' => $this->engine,
            'query' => $query,
        ]);

        // Put the source into a filename or a a document array
        if (is_string($examples)) {
            $config['file'] = $examples;
        } else {
            $config['examples'] = $examples;
        }

        $response = $this->request('POST', 'classifications', [
            'headers' => ['content-type' => 'application/json'],
            'body' => json_encode($config),
        ]);

        return json_decode($response->getBody()->getContents());
    }
}
