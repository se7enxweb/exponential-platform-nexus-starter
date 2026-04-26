<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\EventSubscriber;

use Ibexa\Contracts\Core\Repository\Events\Trash\RecoverEvent;
use Ibexa\Contracts\Core\Repository\Events\Trash\TrashEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\TrashItem;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Trash\Recover;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Trash\Trash;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class TrashEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            RecoverEvent::class => 'onRecover',
            TrashEvent::class => 'onTrash',
        ];
    }

    public function onRecover(RecoverEvent $event): void
    {
        $this->messageBus->dispatch(
            new Recover(
                $event->getLocation()->id,
            ),
        );
    }

    public function onTrash(TrashEvent $event): void
    {
        $this->messageBus->dispatch(
            new Trash(
                $event->getLocation()->id,
                $event->getLocation()->parentLocationId,
                $event->getLocation()->contentId,
                $event->getTrashItem() instanceof TrashItem,
            ),
        );
    }
}
