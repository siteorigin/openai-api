<?php

namespace SiteOrigin\OpenAI;

class Edits extends Action
{
    public function __construct(Client $client, string $model = Models::TEXT_DAVINCI_EDIT, array $config = [])
    {
        parent::__construct($client, 'edits', 'input', 'choices', $model, $config);
    }

    /**
     * Complete the given text.
     *
     * @param string $input
     * @param string $instruction
     * @param array $config Any additional config
     *
     * @return object
     * @see https://beta.openai.com/docs/api-reference/completions/create
     */
    public function edit(string $input = '', string $instruction, array $config = []): object
    {
        return $this->action($input, array_merge($config, ['instruction' => $instruction]));
    }
}
