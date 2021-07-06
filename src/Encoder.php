<?php

namespace SiteOrigin\OpenAI;

/**
 * Class Encoder
 *
 * @package SiteOrigin\OpenAI
 * @see https://github.com/latitudegames/GPT-3-Encoder/blob/master/Encoder.js
 * @see https://github.com/openai/gpt-2/blob/master/src/encoder.py
 */
class Encoder
{
    const EOF_STRING = '<|endoftext|>';
    const EOF_INT = 50256;

    private array $encoder;

    /**
     * @var string[]
     */
    private array $bpe;

    public function __construct()
    {
        //$this->encoder = json_decode(file_get_contents(__DIR__.'/../data/encoder.json'), true);
        //$this->bpe = explode("\n", file_get_contents(__DIR__.'/../data/vocab.bpe.txt'));
    }

    public function encode(string $text): array
    {
    }

    public function decode(array $tokens): string
    {
    }
}
