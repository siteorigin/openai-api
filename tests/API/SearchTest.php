<?php

namespace SiteOrigin\OpenAI\Tests\API;

use SiteOrigin\OpenAI\Tests\BaseTestCase;

class SearchTest extends BaseTestCase
{
    public function test_search_documents()
    {
        $documents = [
            "White House",
            "hospital",
            "school",
        ];

        $result = $this->getClient()->search()->search('President', $documents);
        $this->assertNotEmpty($result->data);
        $this->assertCount(3, $result->data);
    }
}
