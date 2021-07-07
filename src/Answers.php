<?php

namespace SiteOrigin\OpenAI;

class Answers extends Request
{
    private string $engine;

    private array $config;

    /**
     * @param \SiteOrigin\OpenAI\Client $client
     * @param string $engine The engine to use for searching.
     * @param array $config Default config settings.
     */
    public function __construct(Client $client, string $engine = Engines::ADA, array $config = [])
    {
        parent::__construct($client);
        $this->engine = $engine;
        $this->config = $config;
    }

    /**
     * @param string $engine The engine we'll use for the next answer we generate.
     * @return $this
     */
    public function setEngine(string $engine): static
    {
        $this->engine = $engine;

        return $this;
    }

    /**
     * Perform an answer operation.
     *
     * @param string $question The query we want to search for.
     * @param string|array $documents Config options added to the initially set default config.
     * @param string $examplesContext An exaple of a string we'll extract answers from.
     * @param array $examples An array of example questions and answers.
     * @param array $config Additional config options.
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @see https://beta.openai.com/docs/api-reference/answers/create
     */
    public function create(string $question, string | array $documents, string $examplesContext, array $examples, array $config = []): object
    {
        $config = array_merge($this->config, $config);
        $config = array_merge($config, [
            'model' => $this->engine,
            'question' => $question,
            'examples_context' => $examplesContext,
            'examples' => $examples,
        ]);

        // Put the source into a filename or a a document array
        if (is_string($documents)) {
            $config['file'] = $documents;
        } else {
            $config['documents'] = $documents;
        }

        $response = $this->request('POST', sprintf('answers', $this->engine), [
            'headers' => ['content-type' => 'application/json'],
            'body' => json_encode($config),
        ]);

        return json_decode($response->getBody()->getContents());
    }
}
