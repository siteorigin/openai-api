<?php

namespace SiteOrigin\OpenAI\Tests\API;

use PHPUnit\Framework\TestCase;
use SiteOrigin\OpenAI\Encoder;

/**
 * Class EncoderTest
 *
 * @TODO Move this to the API folder once all tests are passing.
 */
class EncoderTest extends TestCase
{
    public function test_encode_string()
    {
        $encoder = new Encoder();
        $encoded = $encoder->encode('Oh, what a wonderful world.');
        $this->assertEquals([5812, 11, 644, 257, 7932, 995, 13], $encoded);

        $encoded = $encoder->encode('👨👴🚗🕒');
        $this->assertEquals([41840, 101, 41840, 112, 8582, 248, 245, 8582, 243, 240], $encoded);

        $encoded = $encoder->encode("À tout à l'heure!");
        $this->assertEquals([127, 222, 256, 448, 28141, 300, 6, 258, 495, 0], $encoded);
    }

    public function test_decode_string()
    {
        $encoder = new Encoder();
        $decoded = $encoder->decode([5812, 11, 644, 257, 7932, 995, 13]);
        $this->assertEquals('Oh, what a wonderful world.', $decoded);

        $decoded = $encoder->decode([41840, 101, 41840, 112, 8582, 248, 245, 8582, 243, 240]);
        $this->assertEquals('👨👴🚗🕒', $decoded);

        $decoded = $encoder->decode([127, 222, 256, 448, 28141, 300, 6, 258, 495, 0]);
        $this->assertEquals("À tout à l'heure!", $decoded);
    }
}
