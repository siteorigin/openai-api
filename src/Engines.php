<?php

namespace SiteOrigin\OpenAI;

class Engines extends Request
{
    /**
     * Return a list of engines
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list(): object
    {
        return json_decode($this->request('get', 'engines')->getBody()->getContents());
    }
}
