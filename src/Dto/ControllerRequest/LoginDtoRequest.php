<?php

namespace App\Dto\ControllerRequest;

use JMS\Serializer\Annotation;
use App\Component\Interface\AbstractDtoControllerRequest;

class LoginDtoRequest extends AbstractDtoControllerRequest
{
    /**
     * Логин
     *
     * @var string
     * @Annotation\Type("string")
     * @Annotation\SerializedName("login")
     */
    public string $login;

    /**
     * Пароль
     *
     * @var string
     * @Annotation\Type("string")
     * @Annotation\SerializedName("password")
     */
    public string $password;
}
