<?php

namespace SiteOrigin\OpenAI;

abstract class Request
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}
