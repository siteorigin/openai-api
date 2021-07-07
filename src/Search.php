<?php

namespace SiteOrigin\OpenAI;

class Search extends Request
{
    private string $engine;

    private array $config;

    /**
     * @param \SiteOrigin\OpenAI\Client $client
     * @param string $engine The engine to use for searching.
     * @param array $config Default config settings.
     */
    public function __construct(Client $client, string $engine = Engines::ADA, array $config = [])
    {
        parent::__construct($client);
        $this->engine = $engine;
        $this->config = $config;
    }

    /**
     * @param string $engine The engine we'll use for the next search.
     * @return $this
     */
    public function setEngine(string $engine): static
    {
        $this->engine = $engine;

        return $this;
    }

    /**
     * Perform a search operation.
     *
     * @param string $query The query we want to search for.
     * @param string|array $documents File ID, or an array of documents.
     * @param array $config Config options added to the initially set default config.
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @see https://beta.openai.com/docs/api-reference/searches/create
     */
    public function search(string $query, string | array $documents, array $config = []): object
    {
        $config = array_merge($this->config, $config);
        $config = array_merge($config, ['query' => $query]);

        // Put the source into a filename or a a document array
        if (is_string($documents)) {
            $config['file'] = $documents;
        } else {
            $config['documents'] = $documents;
        }

        $response = $this->request('POST', sprintf('engines/%s/search', $this->engine), [
            'headers' => ['content-type' => 'application/json'],
            'body' => json_encode($config),
        ]);

        return json_decode($response->getBody()->getContents());
    }
}
