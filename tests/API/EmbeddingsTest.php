<?php

namespace SiteOrigin\OpenAI\Tests\API;

use SiteOrigin\OpenAI\Tests\BaseTestCase;

class EmbeddingsTest extends BaseTestCase
{
    public function test_get_embeddings()
    {
        $client = $this->getClient();
        $response = $client->embeddings('text-similarity-ada-001')->embed([
            'app',
            'blog',
            'product',
            'technology',
            'fashion',
            'business',
            'label',
        ]);

        $this->assertCount(7, $response->data);
        $this->assertCount(1024, $response->data[0]->embedding);
    }
}
