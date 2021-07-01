<?php

namespace SiteOrigin\OpenAI;

class Engines extends Request
{
    /**
     * Return a list of engines
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list()
    {
        $client = $this->client->guzzleClient();
        $engines = $client->get('engines')->getBody()->getContents();
        return json_decode($engines)->data ?? [];
    }
}
