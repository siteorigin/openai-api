<?php

namespace SiteOrigin\OpenAI\Tests\API;

use SiteOrigin\OpenAI\Tests\BaseTestCase;

class FilterTest extends BaseTestCase
{
    public function test_content_filter()
    {
        // Generally this should be considered unsafe
        $r = $this->getClient()->filter()->classify('pornography');
        $this->assertEquals('unsafe', $r);

        // Test lowering the sensitivity
        $r = $this->getClient()->filter(log(0.99))->classify('pornography');
        $this->assertEquals('sensitive', $r);

        // A very safe sentence
        $r = $this->getClient()->filter()->classify('ponies are fantastic!');
        $this->assertEquals('safe', $r);
    }

    public function test_filter_concurrent()
    {
        $items = array_map(
            fn ($item) => trim($item),
            file(__DIR__ . '/../data/products.txt')
        );

        $classifications = $this->getClient()->filter()->classify($items);
        $this->assertCount(160, $classifications);
    }
}
