<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Tests\Integration\Implementation\Common\EventSubscriber;

use Ibexa\Contracts\Core\Persistence\Handler as PersistenceHandler;
use Ibexa\Contracts\Core\Repository\Events\Content\DeleteContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\DeleteTranslationEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\PublishVersionEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\DeleteLocationEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\HideLocationEvent;
use Ibexa\Contracts\Core\Repository\Events\Location\UnhideLocationEvent;
use Ibexa\Contracts\Core\Repository\Events\Trash\RecoverEvent;
use Ibexa\Contracts\Core\Repository\Events\Trash\TrashEvent;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Search\Handler as SearchHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TestChildUpdatesParent implements EventSubscriberInterface
{
    private const PARENT_CONTENT_TYPE_IDENTIFIER = 'extra_fields_test';
    private const CHILD_CONTENT_TYPE_IDENTIFIER = 'extra_fields_test_comment';

    protected SearchHandler $searchHandler;
    protected PersistenceHandler $persistenceHandler;

    public function __construct(
        SearchHandler $searchHandler,
        PersistenceHandler $persistenceHandler
    ) {
        $this->searchHandler = $searchHandler;
        $this->persistenceHandler = $persistenceHandler;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PublishVersionEvent::class => 'onPublishVersion',
            DeleteContentEvent::class => 'onDeleteContent',
            DeleteTranslationEvent::class => 'onDeleteTranslation',
            DeleteLocationEvent::class => 'onDeleteLocation',
            HideLocationEvent::class => 'onHideLocation',
            UnhideLocationEvent::class => 'onUnhideLocation',
            TrashEvent::class => 'onTrash',
            RecoverEvent::class => 'onRecover',
        ];
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function onPublishVersion(PublishVersionEvent $event): void
    {
        $this->handleEvent($event->getContent()->id);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function onDeleteContent(DeleteContentEvent $event): void
    {
        $this->handleEvent($event->getContentInfo()->id);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function onDeleteTranslation(DeleteTranslationEvent $event): void
    {
        $this->handleEvent($event->getContentInfo()->id);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function onDeleteLocation(DeleteLocationEvent $event): void
    {
        $this->handleEvent($event->getLocation()->contentId);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function onHideLocation(HideLocationEvent $event): void
    {
        $this->handleEvent($event->getLocation()->contentId);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function onUnhideLocation(UnhideLocationEvent $event): void
    {
        $this->handleEvent($event->getRevealedLocation()->contentId);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function onTrash(TrashEvent $event): void
    {
        $this->handleEvent($event->getLocation()->contentId);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function onRecover(RecoverEvent $event): void
    {
        $this->handleEvent($event->getLocation()->contentId);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    private function handleEvent(int $contentId): void
    {
        $contentHandler = $this->persistenceHandler->contentHandler();

        try {
            $contentInfo = $contentHandler->loadContentInfo($contentId);
        } catch (NotFoundException) {
            return;
        }

        $contentType = $this->persistenceHandler->contentTypeHandler()->load($contentInfo->contentTypeId);

        if ($contentType->identifier !== self::CHILD_CONTENT_TYPE_IDENTIFIER) {
            return;
        }

        try {
            $location = $this->persistenceHandler->locationHandler()->load($contentInfo->mainLocationId);
            $parentLocation = $this->persistenceHandler->locationHandler()->load($location->parentId);
            $parentContentInfo = $contentHandler->loadContentInfo($parentLocation->contentId);
        } catch (NotFoundException) {
            return;
        }

        $parentContentType = $this->persistenceHandler->contentTypeHandler()->load($parentContentInfo->contentTypeId);

        if ($parentContentType->identifier !== self::PARENT_CONTENT_TYPE_IDENTIFIER) {
            return;
        }

        $this->searchHandler->indexContent(
            $contentHandler->load($parentContentInfo->id, $parentContentInfo->currentVersionNo),
        );
    }
}
