<?php

namespace SiteOrigin\OpenAI\Tests\API;

use SiteOrigin\OpenAI\Tests\BaseTestCase;

class CompleteTest extends BaseTestCase
{
    /** @test */
    public function test_basic_complete()
    {
        $client = $this->getClient();
        $client->completions()->complete('My favorite thing is', [
            'max_tokens' => 8,
            'temperature' => 0.7,
            'n' => 4,
        ]);
    }
}
