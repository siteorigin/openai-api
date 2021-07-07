<?php

namespace SiteOrigin\OpenAI\Tests\API;

use SiteOrigin\OpenAI\Tests\BaseTestCase;

class ClassificationsTest extends BaseTestCase
{
    public function test_classify_document()
    {
        $examples = [
            ["A happy moment", "Positive"],
            ["I am sad.", "Negative"],
            ["I am feeling awesome", "Positive"],
        ];

        $result = $this->getClient()->classifications()->create('It is a raining day :(', $examples);
        $this->assertEquals('Negative', $result->label);
    }
}
