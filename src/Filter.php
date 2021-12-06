<?php

namespace SiteOrigin\OpenAI;

class Filter extends Request
{
    const ENGINE = 'content-filter-alpha-c4';
    const TOXIC_THRESHOLD = -0.355;

    private float $toxicThreshold;

    public static array $labels = [
        0 => 'safe',
        1 => 'sensitive',
        2 => 'unsafe',
    ];

    protected array $config = [
        'max_tokens' => 1,
        'temperature' => 0.,
        'top_p' => 0,
        'logprobs' => 5,
    ];

    private Completions $completion;

    public function __construct(Client $client, float $toxicThreshold = self::TOXIC_THRESHOLD, array $config = [])
    {
        parent::__construct($client);

        $this->config = array_merge($this->config, $config);

        $this->completion = $this->client->completions(self::ENGINE, $this->config);
        $this->toxicThreshold = $toxicThreshold;
    }

    private function complete(array $text)
    {
        $prompts = array_map(fn ($t) => "<|endoftext|>" . $t . "\n--\nLabel:", $text);

        return $this->completion->complete($prompts);
    }

    public function classify(string|array $text): string|array
    {
        $r = $this->complete(is_string($text) ? [$text] : $text);

        $result = array_map(
            function ($choice) {
                $label = (int) $choice->text;
                if ($label == 2) {
                    $probs = (array) $choice->logprobs->top_logprobs[0];
                    if ($probs[2] < $this->toxicThreshold) {
                        $prob0 = $probs[0] ?? 0;
                        $prob1 = $probs[1] ?? 0;

                        if ($prob0 && $prob1) {
                            $label = $prob0 > $prob1 ? 0 : 1;
                        } elseif ($prob0) {
                            $label = 0;
                        } elseif ($prob1) {
                            $label = 1;
                        }
                    }
                }

                if (! in_array($label, array_keys(static::$labels))) {
                    $label = 2;
                }

                return static::$labels[$label];
            },
            $r->choices
        );

        if (is_string($text)) {
            return $result[0];
        }

        // Make sure that the keys match the original input
        $return = [];
        $keys = array_keys($text);
        foreach ($result as $i => $v) {
            $return[$keys[$i]] = $v;
        }

        return $return;
    }
}
