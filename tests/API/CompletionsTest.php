<?php

namespace SiteOrigin\OpenAI\Tests\API;

use SiteOrigin\OpenAI\Engines;
use SiteOrigin\OpenAI\Exception\AuthorizationException;
use SiteOrigin\OpenAI\Tests\BaseTestCase;

class CompletionsTest extends BaseTestCase
{
    /** @test */
    public function test_basic_complete()
    {
        $client = $this->getClient();
        $c = $client->completions('curie')->complete("We could all use some more", [
            'max_tokens' => 32,
            'temperature' => 0.8,
            'n' => 4,
            'stop' => ["\n", '.'],
        ]);
        $this->assertNotEmpty($c->choices);
    }

    public function test_invalid_key_exception()
    {
        $this->expectException(AuthorizationException::class);
        $c = $this->getClient('INVALID')->completions('curie')->complete('My favorite thing is', [
            'max_tokens' => 6,
            'temperature' => 0.7,
            'n' => 4,
        ]);
    }

    public function test_multiple_concurrent()
    {
        $client = $this->getClient();

        $prompts = [
            'Every little thing gonna be',
            'Yesterday, all my troubles seemed so',
            'Hello darkness my old',
            "Life is what happens when you’re busy making other",
            "You must be the change you wish to see in the",
        ];

        $completions = $client->completions(Engines::BABBAGE)->completeMultiple(
            array_chunk($prompts, 2),
            [
                'max_tokens' => 32,
                'temperature' => 0,
                'stop' => ["\n", '.', ','],
            ],
            true
        );

        $this->assertEquals(' alright', $completions[0]->text);
        $this->assertEquals(' far away', $completions[1]->text);
        $this->assertEquals(' friend', $completions[2]->text);
        $this->assertEquals(' plans', $completions[3]->text);
        $this->assertEquals(' world', $completions[4]->text);
    }
}
