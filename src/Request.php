<?php

namespace SiteOrigin\OpenAI;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use SiteOrigin\OpenAI\Exception\AuthorizationException;
use SiteOrigin\OpenAI\Exception\BadRequestException;
use SiteOrigin\OpenAI\Exception\ConflictException;
use SiteOrigin\OpenAI\Exception\NotFoundException;

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
     */
    protected function request(string $method, string $uri = '', array $options = []): ResponseInterface
    {
        try {
            return $this->client->guzzleClient()->request($method, $uri, $options);
        } catch (ClientException $e) {
            throw match ($e->getResponse()->getStatusCode()) {
                400 => new BadRequestException($e),
                401 => new AuthorizationException($e),
                404 => new NotFoundException($e),
                409 => new ConflictException($e),
                default => new RequestException($e),
            };
        } catch (GuzzleException $e) {
            throw new RequestException($e);
        }
    }

    /**
     * Make an async request
     *
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    protected function requestAsync(string $method, string $uri = '', array $options = []): \GuzzleHttp\Promise\PromiseInterface
    {
        return $this->client->guzzleClient()->requestAsync($method, $uri, $options);
    }
}
