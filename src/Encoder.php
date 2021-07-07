<?php

namespace SiteOrigin\OpenAI;

/**
 * Class Encoder.
 *
 * @see https://github.com/openai/gpt-2/blob/master/src/encoder.py
 *
 */
class Encoder
{
    const EOF_STRING = '<|endoftext|>';
    const EOF_INT = 50256;
    const PATTERN = "/'s|'t|'re|'ve|'m|'ll|'d| ?\p{L}+| ?\p{N}+| ?[^\s\p{L}\p{N}]+|\s+(?!\S)|\s+/gu";

    private array $encoder;

    private array $bpeRanks;

    private array $cache;

    public function __construct()
    {
        // Load the encoder
        $this->encoder = json_decode(file_get_contents(__DIR__.'/../data/encoder.json'), true);

        // Load the BPE data and remove the first line
        $bpeData = explode("\n", file_get_contents(__DIR__.'/../data/vocab.bpe.txt'));
        array_shift($bpeData);
        $bpeMerges = array_map(fn ($l) => explode(' ', $l), $bpeData);
        $this->bpeRanks = array_map(null, $bpeMerges, range(0, count($bpeMerges)));
        $this->cache = [];
    }

    protected function getPairs($word): array
    {
        $pairs = [];
        $prevChar = $word[0] ?? '';
        for ($i = 1; $i < strlen($word); ++$i) {
            $pairs[] = [$prevChar, $word[$i]];
            $prevChar = $word[$i];
        }

        return $pairs;
    }

    public function bpe($token)
    {
        if (isset($this->cache[$token])) {
            return $this->cache[$token];
        }

        $pairs = $this->getPairs($token);

        if (empty($pairs)) {
            return $token;
        }

        while (true) {
            //bigram = min(pairs, key = lambda pair: self.bpe_ranks.get(pair, float('inf')))
            $bigram = 0;
        }
    }

    public function encode(string $text): array
    {
        $bpeTokens = [];
        preg_match_all(self::PATTERN, $text, $tokens);
        $tokens = $tokens[0] ?? [];
        foreach ($tokens as $token) {
            //$token = array_map(fn ($t) => utf8_decode($t), str_split($token));
            $token = utf8_decode($token);

            dd($this->bpe($token));
            // bpe_tokens.extend(self.encoder[bpe_token] for bpe_token in self.bpe(token).split(' '))
        }
    }

    public function decode(array $tokens): string
    {
    }
}
