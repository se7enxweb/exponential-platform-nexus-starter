<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\EventSubscriber;

use Ibexa\Contracts\Core\Repository\Events\Location\CopySubtreeEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\CreateLocationEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\DeleteLocationEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\HideLocationEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\MoveSubtreeEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\SwapLocationEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\UnhideLocationEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\UpdateLocationEvent;
use Ibexa\Contracts\Core\Repository\Events\Section\AssignSectionToSubtreeEvent;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Location\AssignSectionToSubtree;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Location\CopySubtree;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Location\CreateLocation;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Location\DeleteLocation;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Location\HideLocation;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Location\MoveSubtree;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Location\SwapLocation;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Location\UnhideLocation;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Location\UpdateLocation;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class LocationEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            AssignSectionToSubtreeEvent::class => 'onAssignSectionToSubtree',
            CopySubtreeEvent::class => 'onCopySubtree',
            CreateLocationEvent::class => 'onCreateLocation',
            DeleteLocationEvent::class => 'onDeleteLocation',
            HideLocationEvent::class => 'onHideLocation',
            MoveSubtreeEvent::class => 'onMoveSubtree',
            SwapLocationEvent::class => 'onSwapLocation',
            UnhideLocationEvent::class => 'onUnhideLocation',
            UpdateLocationEvent::class => 'onUpdateLocation',
        ];
    }

    public function onCopySubtree(CopySubtreeEvent $event): void
    {
        $this->messageBus->dispatch(
            new CopySubtree(
                $event->getLocation()->id,
            ),
        );
    }

    public function onCreateLocation(CreateLocationEvent $event): void
    {
        $this->messageBus->dispatch(
            new CreateLocation(
                $event->getLocation()->id,
                $event->getContentInfo()->id,
            ),
        );
    }

    public function onDeleteLocation(DeleteLocationEvent $event): void
    {
        $this->messageBus->dispatch(
            new DeleteLocation(
                $event->getLocation()->id,
                $event->getLocation()->parentLocationId,
                $event->getLocation()->contentId,
            ),
        );
    }

    public function onHideLocation(HideLocationEvent $event): void
    {
        $this->messageBus->dispatch(
            new HideLocation(
                $event->getHiddenLocation()->id,
            ),
        );
    }

    public function onMoveSubtree(MoveSubtreeEvent $event): void
    {
        $this->messageBus->dispatch(
            new MoveSubtree(
                $event->getLocation()->id,
                $event->getLocation()->parentLocationId,
                $event->getNewParentLocation()->id,
            ),
        );
    }

    public function onSwapLocation(SwapLocationEvent $event): void
    {
        $this->messageBus->dispatch(
            new SwapLocation(
                $event->getLocation1()->id,
                $event->getLocation1()->contentId,
                $event->getLocation2()->id,
                $event->getLocation2()->contentId,
            ),
        );
    }

    public function onUnhideLocation(UnhideLocationEvent $event): void
    {
        $this->messageBus->dispatch(
            new UnhideLocation(
                $event->getRevealedLocation()->id,
            ),
        );
    }

    public function onUpdateLocation(UpdateLocationEvent $event): void
    {
        $this->messageBus->dispatch(
            new UpdateLocation(
                $event->getLocation()->id,
                $event->getLocation()->contentId,
            ),
        );
    }

    public function onAssignSectionToSubtree(AssignSectionToSubtreeEvent $event): void
    {
        $this->messageBus->dispatch(
            new AssignSectionToSubtree(
                $event->getLocation()->id,
            ),
        );
    }
}
