<?php

namespace SiteOrigin\OpenAI\Utils;

use Closure;
use SiteOrigin\OpenAI\Client;

class ListGenerator
{
    private static array $defaultConfig = [
        'max_tokens' => 64,
        'temperature' => 0.9125,
        'presence_penalty' => 0.5,
    ];

    private Closure | iterable $seed;

    private ?string $prompt;

    private string $engine;

    private array $config;

    /**
     * @var \SiteOrigin\OpenAI\Client
     */
    private Client $client;

    /**
     * @var callable|null
     */
    private $itemToString;

    /**
     * ListGenerator constructor.
     *
     * @param \SiteOrigin\OpenAI\Client $client The OpenAI Client
     * @param iterable|callable $seed The seeds or seed generating function
     * @param callable|null $itemToString Convert seed items into a string.
     * @param string $engine The GPT-3 engine to use.
     * @param int $chunkSize How many items to generate with each request.
     * @param array $config The complete call config.
     */
    public function __construct(
        Client $client,
        iterable | callable $seed,
        callable $itemToString = null,
        string $engine = "davinci",
        int $chunkSize = 5,
        array $config = []
    ) {
        $this->client = $client;
        $this->seed = $seed;
        $this->itemToString = $itemToString;

        $this->engine = $engine;
        $this->config = array_merge(self::$defaultConfig, $config, [
            'n' => $chunkSize,
            'stop' => ["\n"],
            'logit_bias' => [
                // Don't let this quit
                50256 => -100,
            ],
        ]);
    }

    /**
     *
     *
     * @param $prompt
     * @return $this
     */
    public function withPrompt($prompt): self
    {
        $this->prompt = $prompt;

        return $this;
    }

    /**
     * Get the input that we'll feed into the completer.
     *
     * @return string
     */
    protected function getCompleterInput(): string
    {
        $seed = is_callable($this->seed) ? call_user_func($this->seed) : $this->seed;
        $input = array_map(function ($s) {
            if (! is_null($this->itemToString)) {
                return call_user_func($this->itemToString, $s);
            } elseif (is_scalar($s)) {
                return $s;
            } else {
                return null;
            }
        }, $seed);
        $input = array_filter($input, fn ($s) => ! is_null($s));

        // Add list indicators
        $input = array_map(fn ($s) => '* ' . $s, $input);

        // Encourage the completion of a new item.
        $input[] = '*';

        $input = implode("\n\n", $input);
        if (! empty($this->prompt)) {
            $input = $this->prompt . "\n\n" . $input;
        }

        return $input;
    }

    /**
     * The generator function.
     *
     * @return \Generator
     */
    public function generate(): \Generator
    {
        $complete = $this->client->completions($this->engine, $this->config);

        while (true) {
            if (empty($input) || is_callable($this->seed)) {
                $input = $this->getCompleterInput();
            }

            $results = $complete->complete($input)->choices;
            foreach ($results as $result) {
                yield trim($result->text);
            }
        }
    }
}
