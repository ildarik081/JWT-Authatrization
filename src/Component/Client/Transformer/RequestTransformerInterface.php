<?php

namespace App\Component\Client\Transformer;

use App\Component\Interface\Client\ClientRequestInterface;
use Psr\Http\Message\RequestInterface as PsrRequestInterface;

interface RequestTransformerInterface
{
    public function createPsrRequest(
        string $method,
        string $fullPath,
        ClientRequestInterface|array $request
    ): PsrRequestInterface;
}
