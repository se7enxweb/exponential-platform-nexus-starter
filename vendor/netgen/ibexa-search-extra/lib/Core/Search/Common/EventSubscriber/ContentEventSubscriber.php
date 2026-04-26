<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\EventSubscriber;

use Ibexa\Contracts\Core\Repository\Events\Content\BeforeDeleteContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\CopyContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\DeleteContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\DeleteTranslationEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\HideContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\PublishVersionEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\RevealContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\UpdateContentMetadataEvent;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Content\CopyContent;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Content\DeleteContent;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Content\DeleteTranslation;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Content\HideContent;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Content\PublishVersion;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Content\RevealContent;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Content\UpdateContentMetadata;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

class ContentEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            CopyContentEvent::class => 'onCopyContent',
            BeforeDeleteContentEvent::class => 'onBeforeDeleteContent',
            DeleteContentEvent::class => 'onDeleteContent',
            DeleteTranslationEvent::class => 'onDeleteTranslation',
            HideContentEvent::class => 'onHideContent',
            PublishVersionEvent::class => 'onPublishVersion',
            RevealContentEvent::class => 'onRevealContent',
            UpdateContentMetadataEvent::class => 'onUpdateContentMetadata',
        ];
    }

    public function onCopyContent(CopyContentEvent $event): void
    {
        $this->messageBus->dispatch(
            new CopyContent(
                $event->getContent()->getVersionInfo()->getContentInfo()->id,
                $event->getContent()->getVersionInfo()->versionNo,
            ),
        );
    }

    public function onBeforeDeleteContent(BeforeDeleteContentEvent $event): void
    {
        try {
            $event->getContentInfo()->getMainLocation()?->parentLocationId;
        } catch (Throwable) {
            // does nothing
        }
    }

    public function onDeleteContent(DeleteContentEvent $event): void
    {
        try {
            $mainLocationParentLocationId = $event->getContentInfo()->getMainLocation()?->parentLocationId;
        } catch (Throwable) {
            $mainLocationParentLocationId = null;
        }

        $this->messageBus->dispatch(
            new DeleteContent(
                $event->getContentInfo()->id,
                $event->getLocations(),
                $mainLocationParentLocationId,
            ),
        );
    }

    public function onDeleteTranslation(DeleteTranslationEvent $event): void
    {
        $this->messageBus->dispatch(
            new DeleteTranslation(
                $event->getContentInfo()->id,
                $event->getLanguageCode(),
            ),
        );
    }

    public function onHideContent(HideContentEvent $event): void
    {
        $this->messageBus->dispatch(
            new HideContent(
                $event->getContentInfo()->id,
            ),
        );
    }

    public function onPublishVersion(PublishVersionEvent $event): void
    {
        $this->messageBus->dispatch(
            new PublishVersion(
                $event->getContent()->id,
                $event->getContent()->getVersionInfo()->versionNo,
            ),
        );
    }

    public function onRevealContent(RevealContentEvent $event): void
    {
        $this->messageBus->dispatch(
            new RevealContent(
                $event->getContentInfo()->id,
            ),
        );
    }

    public function onUpdateContentMetadata(UpdateContentMetadataEvent $event): void
    {
        $this->messageBus->dispatch(
            new UpdateContentMetadata(
                $event->getContentInfo()->id,
            ),
        );
    }
}
