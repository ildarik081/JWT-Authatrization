<?php

namespace App\Dto\ControllerResponse;

use JMS\Serializer\Annotation;
use App\Component\Interface\Controller\ControllerResponseInterface;

class ExceptionDtoResponse implements ControllerResponseInterface
{
    /**
     * @var string
     * @Annotation\Type("string")
     */
    public string $code;

    /**
     * @var string
     * @Annotation\Type("string")
     */
    public string $message;
}
