<?php

namespace SiteOrigin\OpenAI\Tests\API;

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
            'stop' => ["\n", '.']
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
}
