<?php

namespace SiteOrigin\OpenAI\Exception;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use RuntimeException;

class BaseApiException extends RuntimeException
{
    private ?string $type;

    public function __construct(ClientException | ServerException $clientException)
    {
        $r = json_decode($clientException->getResponse()->getBody()->getContents());

        $this->type = $r->error->type ?? null;
        $message = $r->error->message ?? null;

        parent::__construct($message, (int) $clientException->getCode(), $clientException);
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }
}
