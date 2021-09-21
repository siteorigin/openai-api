<?php

namespace SiteOrigin\OpenAI\FineTuned;

use SiteOrigin\OpenAI\Client;

class FineTuned
{
    protected Client $client;

    private string $model;

    private array $config;

    public function __construct(Client $client, string $model, array $config = [])
    {
        $this->client = $client;
        $this->model = $model;
        $this->config = $config;
    }

    public function completions(array $config = []): Completions
    {
        $config = array_merge($this->config, $config);

        return new Completions($this->client, $this->model, $config);
    }

    public function trueFalseClassifier(array $labels = ['false', 'true'], string $separator = ' =>', array $config = []): TrueFalseClassifier
    {
        $config = array_merge($this->config, $config);

        return new TrueFalseClassifier($this->client, $this->model, $labels, $separator, $config);
    }

    public function discriminator(array $config = []): Discriminator
    {
        return new Discriminator($this->client, $this->model, $config);
    }
}
