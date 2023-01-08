<?php

namespace App\Component\Interface;

use JMS\Serializer\Annotation;
use App\Dto\JwtInfoDto;
use OpenApi\Annotations as OA;

abstract class AbstractDtoControllerRequest extends AbstractDto
{
    /**
     * Сессия пользователя
     *
     * @var string
     * @Annotation\Type("string")
     * @Annotation\SerializedName("session")
     */
    public string $session;

    /**
     * Информация для объекта берется из заголовка authorization
     *
     * @var JwtInfoDto|null
     * @Annotation\Type("App\Dto\JwtInfoDto")
     * @Annotation\SerializedName("jwtInfo")
     * @OA\Property(readOnly=true)
     */
    public ?JwtInfoDto $jwtInfo = null;
}
