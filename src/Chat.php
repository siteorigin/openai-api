<?php

namespace SiteOrigin\OpenAI;

class Chat extends Action
{
    public function __construct(Client $client, string $model = Models::CHAT, array $config = [])
    {
        parent::__construct($client, 'chat/completions', 'messages', 'choices', $model, $config);
    }

    public function complete(array $messages, array $config = []): object
    {
        return $this->action($messages, $config);
    }

    public function completeConcurrent(array $messages, array $config = []): object
    {
        return $this->actionConcurrent($messages, $config);
    }
}
