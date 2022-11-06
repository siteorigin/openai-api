<?php

namespace SiteOrigin\OpenAI;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Response;

/**
 * @todo Consolidate this with the completion class
 */
class Embeddings extends Action
{

    public function __construct(Client $client, string $model = Models::TEXT_SIMILARITY_ADA, array $config = [])
    {
        parent::__construct($client, 'embeddings', 'data', $model, $config);
    }

    /**
     * Embed the given text.
     *
     * @param string|array $text
     * @param array $config
     * @return object
     */
    public function embed(string | array $text, array $config = []): object
    {
        return $this->action($text, $config);
    }

    /**
     * Perform the embedding request in parallel.
     *
     * @param array $text
     * @param array $config
     * @return object
     */
    public function embedConcurrent(array $text, array $config = []): object
    {
        return $this->actionConcurrent($text, $config);
    }
}
