<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\MessageHandler\Search\Content;

use Ibexa\Contracts\Core\Persistence\Content\ContentInfo;
use Ibexa\Contracts\Core\Persistence\Content\Handler as ContentHandler;
use Ibexa\Contracts\Core\Persistence\Content\Location\Handler as LocationHandler;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Search\ContentTranslationHandler;
use Ibexa\Contracts\Core\Search\Handler as SearchHandler;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Content\DeleteTranslation;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

use function sprintf;

final class DeleteTranslationHandler
{
    public function __construct(
        private readonly ContentHandler $contentHandler,
        private readonly LocationHandler $locationHandler,
        private readonly SearchHandler $searchHandler,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {}

    public function __invoke(DeleteTranslation $message): void
    {
        try {
            $contentInfo = $this->contentHandler->loadContentInfo(
                $message->contentId,
            );
        } catch (NotFoundException) {
            $this->logger->info(
                sprintf(
                    '%s: Content #%d is gone, aborting',
                    $this::class,
                    $message->contentId,
                ),
            );

            return;
        }

        if ($contentInfo->status !== ContentInfo::STATUS_PUBLISHED) {
            return;
        }

        if ($this->searchHandler instanceof ContentTranslationHandler) {
            $this->searchHandler->deleteTranslation(
                $contentInfo->id,
                $message->languageCode,
            );
        }

        try {
            $content = $this->contentHandler->load(
                $contentInfo->id,
                $contentInfo->currentVersionNo,
            );
        } catch (NotFoundException) {
            $this->logger->info(
                sprintf(
                    '%s: Content #%d in version %d is gone, aborting',
                    $this::class,
                    $message->contentId,
                    $contentInfo->currentVersionNo,
                ),
            );

            return;
        }

        $this->searchHandler->indexContent($content);

        $locations = $this->locationHandler->loadLocationsByContent(
            $contentInfo->id,
        );

        foreach ($locations as $location) {
            $this->searchHandler->indexLocation($location);
        }
    }
}
