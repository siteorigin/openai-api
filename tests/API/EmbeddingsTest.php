<?php

namespace SiteOrigin\OpenAI\Tests\API;

use SiteOrigin\OpenAI\Exception\BadRequestException;
use SiteOrigin\OpenAI\Exception\RequestException;
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

    public function test_get_embeddings_with_int_user()
    {
        $client = $this->getClient();
        try {
            $client->embeddings('text-similarity-ada-001', ['user' => 123])->embed([
                'app',
            ]);
        }
        catch (BadRequestException $e) {
            $this->assertEquals(400, $e->getCode());
            $this->assertEquals("123 is not of type 'string' - 'user'", $e->getMessage());
        }
    }
}
