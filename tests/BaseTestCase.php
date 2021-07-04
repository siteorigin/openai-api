<?php

namespace SiteOrigin\OpenAI\Tests;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use SiteOrigin\OpenAI\Client;

class BaseTestCase extends TestCase
{
    protected function setUp(): void
    {
        // Load env files
        (Dotenv::createImmutable(__DIR__.'/..', '.env.testing'))->safeLoad();
        parent::setUp();
    }

    protected function getClient(): Client
    {
        return new Client(apiKey: $_ENV['API_KEY']);
    }
}
