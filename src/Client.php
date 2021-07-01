<?php

namespace SiteOrigin\OpenAI;

use GuzzleHttp\Client as GuzzleClient;

class Client
{
    const VERSION = 'v1';

    private string $apiKey;

    private array $options;

    private GuzzleClient $guzzle;

    public function __construct(string $apiKey, array $options = [])
    {
        $this->apiKey = $apiKey;
        $this->options = $options;
        $this->guzzle = new GuzzleClient([
            'base_uri' => sprintf('https://api.openai.com/%s/', self::VERSION),
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey
            ]
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
}