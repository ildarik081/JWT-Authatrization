<?php

namespace App\Dto\ControllerRequest;

use JMS\Serializer\Annotation;
use App\Component\Interface\AbstractDtoControllerRequest;

class RefreshTokenDtoRequest extends AbstractDtoControllerRequest
{
    /**
     * Refresh token
     *
     * @var string
     * @Annotation\Type("string")
     * @Annotation\SerializedName("refreshToken")
     */
    public string $refreshToken;
}
