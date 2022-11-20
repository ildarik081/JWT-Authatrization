<?php

namespace App\Dto\ControllerRequest;

use App\Component\Interface\AbstractDtoControllerRequest;
use JMS\Serializer\Annotation;

class UserDtoRequest extends AbstractDtoControllerRequest
{
    /**
     * Email
     *
     * @var string|null
     * @Annotation\Type("string")
     * @Annotation\SerializedName("email")
     */
    public ?string $email = null;

    /**
     * Пароль
     *
     * @var string|null
     * @Annotation\Type("string")
     * @Annotation\SerializedName("password")
     */
    public ?string $password = null;

    /**
     * Имя
     *
     * @var string|null
     * @Annotation\Type("string")
     * @Annotation\SerializedName("firstName")
     */
    public ?string $firstName = null;

    /**
     * Фамилия
     *
     * @var string|null
     * @Annotation\Type("string")
     * @Annotation\SerializedName("lastName")
     */
    public ?string $lastName = null;

    /**
     * Отчество
     *
     * @var string|null
     * @Annotation\Type("string")
     * @Annotation\SerializedName("secondName")
     */
    public ?string $secondName = null;

    /**
     * Дата рождения (ДД.ММ.ГГГГ)
     *
     * @var string|null
     * @Annotation\Type("string")
     * @Annotation\SerializedName("dtBirth")
     */
    public ?string $dtBirth = null;

    /**
     * IP
     *
     * @var string|null
     * @Annotation\Type("string")
     * @Annotation\SerializedName("ip")
     */
    public ?string $ip = null;
}
