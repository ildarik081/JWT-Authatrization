<?php

namespace App\EventSubscriber;

use App\Component\Factory\ErrorResponseFactory;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @param ErrorResponseFactory $errorResponseFactory
     */
    public function __construct(
        private readonly ErrorResponseFactory $errorResponseFactory
    ) {
    }

    /**
     * @param ExceptionEvent $event
     *
     * @return void
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $event->setResponse(
            $this->errorResponseFactory->create($exception)
        );
    }

    #[ArrayShape(['kernel.exception' => "string"])] public static function getSubscribedEvents(): array
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}
