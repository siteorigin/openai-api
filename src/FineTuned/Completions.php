<?php

namespace SiteOrigin\OpenAI\FineTuned;

use SiteOrigin\OpenAI\Client;
use SiteOrigin\OpenAI\Completions as BaseCompletions;
use SiteOrigin\OpenAI\Engines;

class Completions extends BaseCompletions
{
    public function __construct(Client $client, string $model, array $config = [])
    {
        $config = array_merge($config, [
            'model' => $model
        ]);
        parent::__construct($client, Engines::ENGINELESS, $config);
    }
}
