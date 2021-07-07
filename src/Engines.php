<?php

namespace SiteOrigin\OpenAI;

class Engines extends Request
{
    const DAVINCI = 'davinci';
    const CURIE = 'curie';
    const BABBAGE = 'babbage';
    const ADA = 'ada';

    const DAVINCI_INSTRUCT = 'davinci-instruct-beta';
    const CURIE_INSTRUCT = 'curie-instruct-beta';

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
