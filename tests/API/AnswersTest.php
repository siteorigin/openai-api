<?php

namespace SiteOrigin\OpenAI\Tests\API;

use SiteOrigin\OpenAI\Tests\BaseTestCase;

class AnswersTest extends BaseTestCase
{
    public function test_answer_question()
    {
        $documents = [
            "Puppy named Bailey is happy.",
            "Puppy named Bella is sad.",
            "Puppy named Molly is hungry.",
        ];

        $result = $this->getClient()->answers()->create(
            'Which puppy is happy?',
            $documents,
            'In 2017, U.S. life expectancy was 78.6 years.',
            [["What is human life expectancy in the United States?","78 years."]],
            ["max_tokens" => 5, "stop" => ["\n", "<|endoftext|>"] ]
        );

        $this->assertStringContainsString('Bailey', $result->answers[0], 'Incorrect answer given.');
    }
}
