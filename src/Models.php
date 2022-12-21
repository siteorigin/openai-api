<?php

namespace SiteOrigin\OpenAI;

class Models extends Request
{
    const TEXT_DAVINCI = 'text-davinci-003';
    const TEXT_CURIE = 'text-curie-001';
    const TEXT_BABBAGE = 'text-babbage-001';
    const TEXT_ADA = 'text-ada-001';

    const TEXT_DAVINCI_INSERT = 'text-davinci-002';
    const TEXT_DAVINCI_EDIT = 'text-davinci-edit-001';

    const TEXT_EMBED = 'text-embedding-ada-002';

    /**
     * Return a list of engines
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list(): object
    {
        return json_decode($this->request('get', 'models')->getBody()->getContents());
    }

    /**
     * Get details about a model.
     *
     * @param string $model
     * @return mixed
     */
    public function get(string $model)
    {
        return json_decode($this->request('get', sprintf('models/%s', $model))->getBody()->getContents());
    }
}
