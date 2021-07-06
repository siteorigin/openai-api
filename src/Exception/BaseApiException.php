<?php

namespace SiteOrigin\OpenAI\Exception;

use GuzzleHttp\Exception\ClientException;
use RuntimeException;

class BaseApiException extends RuntimeException
{
    private ?string $type;

    public function __construct(ClientException $clientException)
    {
        $r = json_decode($clientException->getResponse()->getBody()->getContents());

        $this->type = $r->message->type ?? null;
        $message = $r->message->message ?? null;

        parent::__construct($message, $clientException->getCode(), $clientException);
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }
}
