<?php

namespace App\Dto\ControllerResponse;

use JMS\Serializer\Annotation;
use App\Component\Interface\Controller\ControllerResponseInterface;

class UserDtoResponse implements ControllerResponseInterface
{
    /**
     * Ид пользователя
     *
     * @var int
     * @Annotation\Type("integer")
     * @Annotation\SerializedName("id")
     */
    public int $id;

    /**
     * Email
     *
     * @var string
     * @Annotation\Type("string")
     * @Annotation\SerializedName("email")
     */
    public string $email;

    /**
     * Имя
     *
     * @var string
     * @Annotation\Type("string")
     * @Annotation\SerializedName("firstName")
     */
    public string $firstName;

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
     * Ip адерс
     *
     * @var string|null
     * @Annotation\Type("string")
     * @Annotation\SerializedName("ip")
     */
    public ?string $ip = null;
}
