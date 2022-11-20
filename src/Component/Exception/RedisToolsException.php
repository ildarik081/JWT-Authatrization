<?php

namespace App\Component\Exception;

use Throwable;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class RedisToolsException extends AbstractApiException
{
    public function __construct(
        string $message = self::DEFAULT_MESSAGE,
        int $code = ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
        string $responseCode = self::DEFAULT_RESPONSE_CODE,
        $logLevel = LogLevel::ERROR,
        array $headers = [],
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $responseCode, $logLevel, $headers, $previous);
    }
}
