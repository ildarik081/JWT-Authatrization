<?php

namespace App\Dto\ControllerResponse;

use JMS\Serializer\Annotation;
use App\Component\Interface\Controller\ControllerResponseInterface;

class UserJwtDtoResponse implements ControllerResponseInterface
{
    /**
     * Пользователь
     *
     * @var UserDtoResponse
     * @Annotation\Type("App\Dto\ControllerResponse\UserDtoResponse")
     */
    public UserDtoResponse $user;

    /**
     * Access и refresh token
     *
     * @var JwtDtoResponse
     * @Annotation\Type("App\Dto\ControllerResponse\JwtDtoResponse")
     */
    public JwtDtoResponse $jwt;
}
