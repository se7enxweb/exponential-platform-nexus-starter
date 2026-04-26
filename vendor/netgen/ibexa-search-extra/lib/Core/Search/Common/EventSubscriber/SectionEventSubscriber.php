<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\EventSubscriber;

use Ibexa\Contracts\Core\Repository\Events\Section\AssignSectionEvent;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Section\AssignSection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class SectionEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            AssignSectionEvent::class => 'onAssignSection',
        ];
    }

    public function onAssignSection(AssignSectionEvent $event): void
    {
        $this->messageBus->dispatch(
            new AssignSection(
                $event->getContentInfo()->id,
            ),
        );
    }
}
