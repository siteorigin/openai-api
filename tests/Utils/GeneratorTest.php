<?php

namespace Siteorigin\OpenAI\Tests\Utils;

use SiteOrigin\OpenAI\Tests\BaseTestCase;
use SiteOrigin\OpenAI\Utils\ListGenerator;

class GeneratorTest extends BaseTestCase
{
    public function test_generate_items()
    {
        $variableNames = ['foo', 'bar', 'baz', 'qux', 'frob', 'nix', 'eve'];

        $generator = new ListGenerator(
            $this->getClient(),
            function () use ($variableNames) {
                return array_slice($variableNames, 0, 150);
            },
            engine: 'babbage',
            chunkSize: 3
        );
        $generator->withPrompt('This is a random variable name generator:');

        $this->assertNotEmpty($generator->generate()->current());
        $this->assertNotEmpty($generator->generate()->current());
        $this->assertNotEmpty($generator->generate()->current());
    }
}
