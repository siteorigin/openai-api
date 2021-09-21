<?php

namespace SiteOrigin\OpenAI\Exception;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

/**
 * @todo use the message from OpenAI as the exception message.
 */
class BadRequestException extends BaseApiException
{
}
