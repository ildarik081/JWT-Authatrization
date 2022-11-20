<?php

namespace App\Dto\ControllerResponse;

use JMS\Serializer\Annotation;
use App\Component\Interface\Controller\ControllerResponseInterface;

class JwtDtoResponse implements ControllerResponseInterface
{
    /**
     * Access token
     *
     * @var string
     * @Annotation\Type("string")
     * @Annotation\SerializedName("accessToken")
     */
    public string $accessToken;

    /**
     * Refresh token
     *
     * @var string
     * @Annotation\Type("string")
     * @Annotation\SerializedName("refreshToken")
     */
    public string $refreshToken;
}
