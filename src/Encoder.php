<?php

namespace SiteOrigin\OpenAI;

/**
 * Class Encoder
 *
 * @package SiteOrigin\OpenAI
 * @see https://github.com/latitudegames/GPT-3-Encoder/blob/master/Encoder.js
 */
class Encoder
{
    const END_OF_TEXT_STRING = '<|endoftext|>';
    const END_OF_TEXT_INT = 50256;

    private array $encoder;

    public function __construct()
    {
        $this->encoder = json_decode(file_get_contents(__DIR__.'/../data/encoder.json'), true);
        $this->bpe = explode("\n", file_get_contents(__DIR__.'/../data/encoder.json'));
    }

    public function encode(string $text)
    {

    }

    public function decode(array $tokens)
    {

    }
}