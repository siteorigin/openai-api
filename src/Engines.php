<?php

namespace SiteOrigin\OpenAI;

class Engines extends Request
{
    const ENGINE_DAVINCI = 'davinci';
    const ENGINE_CURIE = 'curie';
    const ENGINE_BABBAGE = 'babbage';
    const ENGINE_ADA = 'ada';

    const ENGINE_DAVINCI_INSTRUCT = 'davinci-instruct-beta';
    const ENGINE_CURIE_INSTRUCT = 'curie-instruct-beta';

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