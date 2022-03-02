<?php

namespace SiteOrigin\OpenAI\FineTuned;

use SiteOrigin\OpenAI\Client;

class Discriminator extends TrueFalseClassifier
{
    public function __construct(
        Client $client,
        string $model,
        array $config = []
    ) {
        parent::__construct($client, $model, ['fake', 'real'], ' ->', $config);
    }

    public function discriminate(array $items): array
    {
        return $this->classify($items);
    }
}
