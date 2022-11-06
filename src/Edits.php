<?php

namespace SiteOrigin\OpenAI;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Response;

class Edits extends Action
{
    public function __construct(Client $client, string $model = Models::TEXT_DAVINCI_EDIT, array $config = [])
    {
        parent::__construct($client, 'edits', 'choices', $model, $config);
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
    public function complete(string | array $prompt = '', array $config = []): object
    {
        return $this->action($prompt, $config);
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
        return $this->actionConcurrent($prompts, $config);
    }
}
