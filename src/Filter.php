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

    public function __construct(Client $client, float $toxicThreshold = self::TOXIC_THRESHOLD)
    {
        parent::__construct($client);
        $this->toxicThreshold = $toxicThreshold;
    }

    private function complete(string|array $text)
    {
        $prompt = is_string($text) ?
            "<|endoftext|>" . $text . "\n--\nLabel:" :
            array_map(fn ($t) => "<|endoftext|>" . $t . "\n--\nLabel:", $text);

        $config = [
            'prompt' => $prompt,
            'max_tokens' => 1,
            'temperature' => 0.,
            'top_p' => 0,
            'logprobs' => 10,
        ];

        $response = $this->request(
            'POST',
            sprintf('engines/%s/completions', self::ENGINE),
            [
                'headers' => ['content-type' => 'application/json'],
                'body' => json_encode($config),
            ]
        );

        return json_decode($response->getBody()->getContents())->choices;
    }

    public function classify(string|array $text): string|array
    {
        $choices = $this->complete($text);
        $return = [];

        foreach ($choices as $choice) {
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

            $return[] = static::$labels[$label];
        }

        return is_string($text) ? $return[0] : $return;
    }
}
