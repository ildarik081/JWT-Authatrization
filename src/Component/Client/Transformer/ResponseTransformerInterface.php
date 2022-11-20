<?php

namespace App\Component\Client\Transformer;

use App\Component\Interface\Client\ClientResponseInterface;
use Psr\Http\Message\ResponseInterface;

interface ResponseTransformerInterface
{
    public function createResponse(
        ResponseInterface $psrResponse,
        string $type
    ): ClientResponseInterface;
}
