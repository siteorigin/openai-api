<?php

namespace SiteOrigin\OpenAI\Tests\API;

use SiteOrigin\OpenAI\Tests\BaseTestCase;

class EmbeddingsTest extends BaseTestCase
{
    public function test_get_embeddings()
    {
        $client = $this->getClient();
        $response = $client->embeddings('ada-search-query')->embed([
            'app',
            'blog',
            'product',
            'technology',
            'fashion',
            'business',
            'label',
            'shop',
            'crypto coin',
            'lifestyle',
            'tool',
            'travel',
            'luxury',
            'health',
            'consulting',
            'food',
            'group',
            'marketing',
            'operations',
            'beverage',
            'beauty',
            'developer',
            'solution',
            'financial',
            'sports',
            'sales',
            'security',
            'clothing',
            'software',
            'art',
        ]);

        $this->assertCount(30, $response->data);
        $this->assertCount(1024, $response->data[0]->embedding);
    }

}
