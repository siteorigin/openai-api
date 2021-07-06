<?php

namespace SiteOrigin\OpenAI;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use SiteOrigin\OpenAI\Exception\AuthorizationException;
use SiteOrigin\OpenAI\Exception\BadRequestException;
use SiteOrigin\OpenAI\Exception\BaseApiException;
use SiteOrigin\OpenAI\Exception\InvalidEndpointException;

abstract class Request
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function request(string $method, string $uri = '', array $options = []): ResponseInterface
    {
        try{
            return $this->client->guzzleClient()->request($method, $uri, $options);
        }
        catch (ClientException $e) {
            throw match ($e->getResponse()->getStatusCode()) {
                400 => new BadRequestException($e),
                401 => new AuthorizationException($e),
                404 => new InvalidEndpointException($e),
                default => $e,
            };
        }
    }
}
