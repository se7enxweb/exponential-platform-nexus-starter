<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\MessageHandler\Search\Location;

use Ibexa\Contracts\Core\Persistence\Content\Handler as ContentHandler;
use Ibexa\Contracts\Core\Persistence\Content\Location\Handler as LocationHandler;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Search\Handler as SearchHandler;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Location\SwapLocation;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

use function sprintf;

final class SwapLocationHandler
{
    public function __construct(
        private readonly ContentHandler $contentHandler,
        private readonly LocationHandler $locationHandler,
        private readonly SearchHandler $searchHandler,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {}

    public function __invoke(SwapLocation $message): void
    {
        $this->reindexForContentLocation(
            $message->content1Id,
            $message->location1Id,
        );

        $this->reindexForContentLocation(
            $message->content2Id,
            $message->location2Id,
        );
    }

    private function reindexForContentLocation(int $contentId, int $locationId): void
    {
        try {
            $content = $this->contentHandler->load(
                $contentId,
                $this->contentHandler->loadContentInfo($contentId)->currentVersionNo,
            );
        } catch (NotFoundException) {
            $this->logger->info(
                sprintf(
                    '%s: Content #%d is gone, aborting',
                    $this::class,
                    $contentId,
                ),
            );

            return;
        }

        $this->searchHandler->indexContent($content);

        try {
            $location = $this->locationHandler->load($locationId);
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

        $this->searchHandler->indexLocation($location);
    }
}
