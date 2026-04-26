<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\EventSubscriber;

use Ibexa\Contracts\Core\Repository\Events\ObjectState\SetContentStateEvent;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\ObjectState\SetContentState;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ObjectStateEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            SetContentStateEvent::class => 'onSetContentState',
        ];
    }

    public function onSetContentState(SetContentStateEvent $event): void
    {
        $this->messageBus->dispatch(
            new SetContentState(
                $event->getContentInfo()->id,
            ),
        );
    }
}
