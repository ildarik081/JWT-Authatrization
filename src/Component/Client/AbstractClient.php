<?php

namespace App\Component\Client;

use App\Component\Client\Transformer\RequestTransformerInterface;
use App\Component\Client\Transformer\ResponseTransformerInterface;
use App\Component\Exception\ClientResponseException;
use App\Component\Interface\Client\ClientRequestInterface;
use App\Component\Interface\Client\ClientResponseInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

abstract class AbstractClient
{
    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly RequestTransformerInterface $requestTransformer,
        private readonly ResponseTransformerInterface $responseTransformer
    ) {
    }

    /**
     * @param string $method
     * @param string $fullPath
     * @param string $type
     *
     * @param ClientRequestInterface|array $request
     *
     * @return ClientResponseInterface
     * @throws ClientExceptionInterface
     * @throws ClientResponseException
     */
    protected function sendRequest(
        string $method,
        string $fullPath,
        string $type,
        ClientRequestInterface|array $request = []
    ): ClientResponseInterface {
        $method = strtoupper($method);
        $psrRequest = $this->requestTransformer->createPsrRequest(
            $method,
            $fullPath,
            $request
        );

        $psrResponse = $this->httpClient->sendRequest($psrRequest);

        $this->checkStatuses($psrResponse);

        return $this->responseTransformer->createResponse($psrResponse, $type);
    }

    /**
     * @throws ClientResponseException
     */
    private function checkStatuses(ResponseInterface $psrResponse): void
    {
        if (
            $psrResponse->getStatusCode() !== ResponseAlias::HTTP_OK &&
            $psrResponse->getStatusCode() !== ResponseAlias::HTTP_CREATED
        ) {
            throw new ClientResponseException(
                "Ошибка запроса. " . $psrResponse->getBody()->getContents(),
                $psrResponse->getStatusCode()
            );
        }
    }
}
