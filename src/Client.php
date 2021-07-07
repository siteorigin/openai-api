<?php

namespace SiteOrigin\OpenAI;

use GuzzleHttp\Client as GuzzleClient;

class Client
{
    const VERSION = 'v1';

    private string $apiKey;

    private array $options;

    private GuzzleClient $guzzle;

    public function __construct(string $apiKey, string $organization = null)
    {
        $this->apiKey = $apiKey;

        $headers = [
            'Authorization' => 'Bearer ' . $this->apiKey,
        ];
        if (!empty($organization)) {
            $headers['OpenAI-Organization'] = $organization;
        }

        $this->guzzle = new GuzzleClient([
            'base_uri' => sprintf('https://api.openai.com/%s/', self::VERSION),
            'headers' => $headers,
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

    public function search(string $engine = Engines::ADA, array $config = []): Search
    {
        return new Search($this, $engine, $config);
    }

    public function classifications(string $engine = Engines::CURIE, array $config = []): Classifications
    {
        return new Classifications($this, $engine, $config);
    }

    public function answers(string $engine = Engines::CURIE, array $config = []): Answers
    {
        return new Answers($this, $engine, $config);
    }
}
