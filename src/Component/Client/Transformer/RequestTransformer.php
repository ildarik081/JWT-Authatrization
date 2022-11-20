<?php

namespace App\Component\Client\Transformer;

use App\Component\Utils\Aliases;
use JMS\Serializer\Serializer;
use App\Component\Interface\Client\ClientRequestInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\RequestInterface as PsrRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

class RequestTransformer implements RequestTransformerInterface
{
    public function __construct(
        private readonly Psr17Factory $requestFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly Serializer $serializer
    ) {
    }

    /**
     * @param string $method
     * @param string $fullPath
     * @param ClientRequestInterface|array $request
     *
     * @return PsrRequestInterface
     * @throws \JsonException
     */
    public function createPsrRequest(
        string $method,
        string $fullPath,
        ClientRequestInterface|array $request
    ): PsrRequestInterface {
        if ($method === Aliases::METHOD_GET) {
            $requestArray = is_array($request) ? $request : $this->serializer->toArray($request);
            $uriString = count($requestArray) > 0 ? $fullPath . '?' . http_build_query($requestArray) : $fullPath;
        } else {
            $uriString = $fullPath;
        }

        $uriObject = $this->requestFactory->createUri($uriString);
        $psrRequest = $this->requestFactory->createRequest($method, $uriObject);

        if ($method === Aliases::METHOD_POST || $method === Aliases::METHOD_PUT || $method === Aliases::METHOD_DELETE) {
            $requestJson = $this->createRequestJson($request);
            $stream = $this->streamFactory->createStream($requestJson);
            $psrRequest = $psrRequest
                ->withHeader('Content-Type', 'application/json')
                ->withBody($stream)
            ;
        }

        return $psrRequest;
    }

    /**
     * @param ClientRequestInterface|array $request
     *
     * @return string
     * @throws \JsonException
     */
    private function createRequestJson(ClientRequestInterface|array $request): string
    {
        if (is_array($request)) {
            return json_encode($request, JSON_THROW_ON_ERROR);
        }

        return $this->serializer->serialize($request, 'json');
    }
}
