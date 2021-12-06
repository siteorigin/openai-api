<?php

namespace SiteOrigin\OpenAI\FineTuned;

use SiteOrigin\OpenAI\Client;

class TrueFalseClassifier extends Completions
{
    /**
     * @var array|string[]
     */
    private array $labels;

    private string $separator;

    public function __construct(
        Client $client,
        string $model,
        array $labels = ['false', 'true'],
        string $separator = ' =>',
        array $config = []
    ) {
        $config = array_merge($config, [
            'max_tokens' => 1,
            'logprobs' => 5,
            'temperature' => 0,
        ]);
        parent::__construct($client, $model, $config);

        $this->labels = $labels;
        $this->separator = $separator;
    }

    public function classify(array $items, array $config = [])
    {
        if(empty($items)) return [];

        $r = $this->complete(
            array_map(fn ($p) => $p . $this->separator, $items),
            $config
        );

        $result = array_map(function ($c) {
            $logProbs = (array) $c->logprobs->top_logprobs[0];
            $weights = [];

            foreach ($this->labels as $i => $label) {
                $weights[$i] = array_sum(array_map(
                    fn ($log) => exp($log),
                    array_intersect_key($logProbs, array_flip([$label, ' ' . $label]))
                ));
            }

            if (empty($weights[0])) {
                return 1.;
            } elseif (empty($weights[1])) {
                return 0.;
            } else {
                return $weights[1] / array_sum($weights);
            }
        }, $r->choices);

        $return = [];
        $keys = array_keys($items);
        foreach ($result as $i => $v) {
            $return[$keys[$i]] = $v;
        }

        return $return;
    }
}
