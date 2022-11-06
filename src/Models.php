<?php

namespace SiteOrigin\OpenAI;

class Models extends Request
{
    const TEXT_DAVINCI = 'text-davinci-002';
    const TEXT_CURIE = 'text-curie-001';
    const TEXT_BABBAGE = 'text-babbage-001';
    const TEXT_ADA = 'text-ada-001';

    const TEXT_DAVINCI_INSERT = 'text-davinci-002';
    const TEXT_DAVINCI_EDIT = 'text-davinci-edit-001';

    const TEXT_SIMILARITY_DAVINCI = 'text-similarity-ada-001';
    const TEXT_SIMILARITY_CURIE = 'text-similarity-curie-001';
    const TEXT_SIMILARITY_BABBAGE = 'text-similarity-babbage-001';
    const TEXT_SIMILARITY_ADA = 'text-similarity-ada-001';

    const TEXT_SEARCH_DAVINCI_DOC = 'text-search-ada-doc-001';
    const TEXT_SEARCH_DAVINCI_QUERY = 'text-search-ada-query-001';
    const TEXT_SEARCH_CURIE_DOC = 'text-search-curie-doc-001';
    const TEXT_SEARCH_CURIE_QUERY = 'text-search-curie-query-001';
    const TEXT_SEARCH_BABBAGE_DOC = 'text-search-babbage-doc-001';
    const TEXT_SEARCH_BABBAGE_QUERY = 'text-search-babbage-query-001';
    const TEXT_SEARCH_ADA_DOC = 'text-search-ada-doc-001';
    const TEXT_SEARCH_ADA_QUERY = 'text-search-ada-query-001';


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
