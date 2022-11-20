<?php

namespace App\Dto\ControllerResponse;

use JMS\Serializer\Annotation;
use App\Component\Interface\Controller\ControllerResponseInterface;

class SuccessDtoResponse implements ControllerResponseInterface
{
    /**
     * Статус
     *
     * @var bool
     * @Annotation\Type("boolean")
     * @Annotation\SerializedName("success")
     */
    public bool $success;
}
