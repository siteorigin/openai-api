<?php

namespace SiteOrigin\OpenAI\Tests\API;

use SiteOrigin\OpenAI\Tests\BaseTestCase;

class FineTuneCompletionsTest extends BaseTestCase
{
    public function test_discriminator()
    {
        $discriminator = $this->getClient()->fineTuned($_ENV['PRODUCT_DISCRIMINATION_MODEL'])->discriminator();

        $items = array_map(
            fn ($item) => trim($item),
            file(__DIR__ . '/../data/products.txt')
        );

        $results = $discriminator->discriminate($items);
        $this->assertNotEmpty($results);
    }
}
