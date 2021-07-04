<?php

namespace SiteOrigin\OpenAI\Tests\API;

use SiteOrigin\OpenAI\Tests\BaseTestCase;

class EngineTest extends BaseTestCase
{
    public function test_list_engines()
    {
        // Lets check that we're able to get a list of engines.
        $client = $this->getClient();
        $engines = $client->engines()->list();
        $this->assertNotEmpty($engines);
    }
}
