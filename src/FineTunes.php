<?php

namespace SiteOrigin\OpenAI;

class FineTunes extends Request
{
    /**
     * Create a new fine tune job
     *
     * @param string $trainingFile
     * @param array $args
     * @return mixed
     *
     * @see https://beta.openai.com/docs/api-reference/fine-tunes/create
     */
    public function create(string $trainingFile, array $args): mixed
    {
        $config = array_merge($args, ['training_file' => $trainingFile]);

        $response = $this->request('POST', 'fine-tunes', [
            'headers' => ['content-type' => 'application/json'],
            'body' => json_encode($config),
        ]);

        return json_decode($response->getBody()->getContents());
    }

    /**
     * List your organization's fine-tuning jobs
     *
     * @return mixed
     *
     * @see https://beta.openai.com/docs/api-reference/fine-tunes/list
     */
    public function list(): mixed
    {
        $response = $this->request('GET', 'fine-tunes', [
            'headers' => ['content-type' => 'application/json'],
        ]);

        return json_decode($response->getBody()->getContents());
    }

    /**
     * Gets info about the fine-tune job.
     *
     * @param string $fineTuneId
     * @return mixed
     *
     * @see https://beta.openai.com/docs/api-reference/fine-tunes/retrieve
     */
    public function retrieve(string $fineTuneId): mixed
    {
        $response = $this->request('GET', sprintf('fine-tunes/%s', $fineTuneId), [
            'headers' => ['content-type' => 'application/json'],
        ]);

        return json_decode($response->getBody()->getContents());
    }

    /**
     * Immediately cancel a fine-tune job.
     *
     * @param string $fineTuneId
     * @return mixed
     *
     * @see https://beta.openai.com/docs/api-reference/fine-tunes/cancel
     */
    public function cancel(string $fineTuneId): mixed
    {
        $response = $this->request('POST', sprintf('fine-tunes/%s/cancel', $fineTuneId), [
            'headers' => ['content-type' => 'application/json'],
        ]);

        return json_decode($response->getBody()->getContents());
    }

    /**
     * Get fine-grained status updates for a fine-tune job.
     *
     * @param string $fineTuneId
     * @return mixed
     *
     * @see https://beta.openai.com/docs/api-reference/fine-tunes/events
     */
    public function events(string $fineTuneId): mixed
    {
        $response = $this->request('GET', sprintf('fine-tunes/%s/list', $fineTuneId), [
            'headers' => ['content-type' => 'application/json'],
        ]);

        return json_decode($response->getBody()->getContents());
    }
}
