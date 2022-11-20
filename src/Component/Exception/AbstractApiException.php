<?php

namespace App\Component\Exception;

use Exception;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

abstract class AbstractApiException extends Exception
{
    protected const DEFAULT_RESPONSE_CODE = 'UNKNOWN_ERROR';
    protected const DEFAULT_MESSAGE = 'Неизвестная ошибка';
    private string $responseCode;
    private int|string $logLevel;
    private array $headers;

    /**
     * @param string $message
     * @param int $code
     * @param string $responseCode
     * @param string|int $logLevel
     * @param array $headers
     * @param Throwable|null $previous
     */
    public function __construct(
        string $message = self::DEFAULT_MESSAGE,
        int $code = ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
        string $responseCode = self::DEFAULT_RESPONSE_CODE,
        $logLevel = LogLevel::ERROR,
        array $headers = [],
        ?Throwable $previous = null
    ) {
         $this->responseCode = $responseCode;
         $this->logLevel = $logLevel;
         $this->headers = $headers;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getResponseCode(): string
    {
        return $this->responseCode;
    }

    /**
     * @return string|int
     */
    public function getLogLevel(): string|int
    {
        return $this->logLevel;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
