<?php

namespace SiteOrigin\OpenAI;

use GuzzleHttp\Client as GuzzleClient;

class Client
{
    const VERSION = 'v1';

    private string $apiKey;

    private array $options;

    private GuzzleClient $guzzle;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->guzzle = new GuzzleClient([
            'base_uri' => sprintf('https://api.openai.com/%s/', self::VERSION),
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
            ],
        ]);
    }

    public function guzzleClient(): GuzzleClient
    {
        return $this->guzzle;
    }

    public function engines(): Engines
    {
        return new Engines($this);
    }

    public function completions(string $engine = 'davinci', array $config = []): Completions
    {
        return new Completions($this, $engine, $config);
    }

    public function files(): Files
    {
        return new Files($this);
    }

    public function search(string $engine = Engines::ENGINE_ADA, array $config = []): Search
    {
        return new Search($this, $engine, $config);
    }

    public function classifications(string $engine = Engines::ENGINE_CURIE, array $config = []): Classifications
    {
        return new Classifications($this, $engine, $config);
    }

    public function answers(string $engine = Engines::ENGINE_CURIE, array $config = []): Answers
    {
        return new Answers($this, $engine, $config);
    }
}
