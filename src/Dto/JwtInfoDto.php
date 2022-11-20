<?php

namespace App\Dto;

use JMS\Serializer\Annotation;

class JwtInfoDto
{
    /**
     * @var UserDto
     * @Annotation\Type("App\Dto\UserDto")
     */
    public UserDto $user;

    /**
     * Время протухания токена
     *
     * @var int
     * @Annotation\Type("integer")
     */
    public int $exp;
}
