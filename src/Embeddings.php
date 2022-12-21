<?php

namespace SiteOrigin\OpenAI;

class Embeddings extends Action
{
    public function __construct(Client $client, string $model = Models::TEXT_EMBED, array $config = [])
    {
        parent::__construct($client, 'embeddings', 'input', 'data', $model, $config);
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
