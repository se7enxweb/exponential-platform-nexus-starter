<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\MessageHandler\Search;

use Ibexa\Contracts\Core\Persistence\Content\Handler as ContentHandler;
use Ibexa\Contracts\Core\Persistence\Content\Location\Handler as LocationHandler;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Search\Handler as SearchHandler;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

use function array_values;
use function sprintf;

final class SubtreeIndexer
{
    public function __construct(
        private readonly ContentHandler $contentHandler,
        private readonly LocationHandler $locationHandler,
        protected readonly SearchHandler $searchHandler,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {}

    public function indexSubtree(int $locationId): void
    {
        try {
            $subtreeIds = $this->locationHandler->loadSubtreeIds($locationId);
        } catch (NotFoundException) {
            $this->logger->info(
                sprintf(
                    '%s: Location #%d is gone, aborting',
                    $this::class,
                    $locationId,
                ),
            );

            return;
        }

        $subtreeContentIds = array_values($subtreeIds);
        $contentInfoList = $this->contentHandler->loadContentInfoList($subtreeContentIds);
        $processedContentIdSet = [];

        foreach ($subtreeIds as $subtreeLocationId => $contentId) {
            try {
                $location = $this->locationHandler->load($subtreeLocationId);
                $this->searchHandler->indexLocation($location);
            } catch (NotFoundException) {
                $this->logger->info(
                    sprintf(
                        '%s: Subtree Location #%d is gone, continuing',
                        $this::class,
                        $subtreeLocationId,
                    ),
                );
            }

            if (isset($processedContentIdSet[$contentId])) {
                continue;
            }

            // Content could be found in multiple Locations of the subtree,
            // but we need to (re)index it only once
            $processedContentIdSet[$contentId] = true;

            if (!isset($contentInfoList[$contentId])) {
                $this->logger->info(
                    sprintf(
                        '%s: Subtree Location Content #%d is gone, continuing',
                        $this::class,
                        $contentId,
                    ),
                );

                continue;
            }

            try {
                $content = $this->contentHandler->load(
                    $contentId,
                    $contentInfoList[$contentId]->currentVersionNo,
                );
                $this->searchHandler->indexContent($content);
            } catch (NotFoundException) {
                $this->logger->info(
                    sprintf(
                        '%s: Subtree Location Content #%d in version %d is gone, continuing',
                        $this::class,
                        $contentId,
                        $contentInfoList[$contentId]->currentVersionNo,
                    ),
                );
            }
        }
    }
}
