<?php

namespace App\EventSubscriber;

use App\Component\Exception\JsonFactoryException;
use App\Component\Factory\DtoResponseFactory;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;

class DtoResponseSubscriber implements EventSubscriberInterface
{
    /**
     * @param DtoResponseFactory $dtoResponseFactory
     */
    public function __construct(private readonly DtoResponseFactory $dtoResponseFactory)
    {
    }

    /**
     * @param ViewEvent $event
     * @return void
     * @throws JsonFactoryException
     */
    public function onKernelView(ViewEvent $event): void
    {
        $response = $this->dtoResponseFactory->create($event->getControllerResult());

        $event->setResponse($response);
    }

    #[ArrayShape(['kernel.view' => "string"])]
    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.view' => 'onKernelView',
        ];
    }
}
