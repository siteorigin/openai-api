<?php

namespace SiteOrigin\OpenAI\Tests\API;

use SiteOrigin\OpenAI\Models;
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

    public function test_multiple_prompts()
    {
        $client = $this->getClient();

        $prompts = [
            'Every little thing gonna be',
            'Yesterday, all my troubles seemed so',
            'Hello darkness my old',
            "Life is what happens when you’re busy making other",
            "You must be the change you wish to see in the",
        ];

        $r = $client->completions(Models::BABBAGE)->complete(
            $prompts,
            [
                'max_tokens' => 32,
                'temperature' => 0,
                'stop' => ["\n", '.', ','],
            ]
        );

        $this->assertEquals(' alright', $r->choices[0]->text);
        $this->assertEquals(' far away', $r->choices[1]->text);
        $this->assertEquals(' friend', $r->choices[2]->text);
        $this->assertEquals(' plans', $r->choices[3]->text);
        $this->assertEquals(' world', $r->choices[4]->text);
    }
}
