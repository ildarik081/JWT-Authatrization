<?php

namespace App\Component\Client\Transformer;

use App\Component\Interface\Client\ClientResponseInterface;
use JMS\Serializer\SerializerInterface;
use Psr\Http\Message\ResponseInterface;

class ResponseTransformer implements ResponseTransformerInterface
{
    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(private readonly SerializerInterface $serializer)
    {
    }

    /**
     * @param ResponseInterface $psrResponse
     * @param string $type
     *
     * @return ClientResponseInterface
     */
    public function createResponse(
        ResponseInterface $psrResponse,
        string $type
    ): ClientResponseInterface {
        $responseJson = $psrResponse->getBody();
        $content = $responseJson->getContents();

        return $this->serializer->deserialize($content, $type, 'json');
    }
}
