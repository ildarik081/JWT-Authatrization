<?php

namespace App\Dto;

use JMS\Serializer\Annotation;

class UserDto
{
    /**
     * @var integer
     * @Annotation\Type("integer")
     * @Annotation\SerializedName("id")
     */
    public int $id;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\SerializedName("ip")
     */
    public string $ip;

    /**
     * @var string
     * @Annotation\Type("string")
     * @Annotation\SerializedName("dtCreate")
     */
    public string $dtCreate;
}
