<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\MessageHandler\Search;

use Ibexa\Contracts\Core\Persistence\Content\Handler as ContentHandler;
use Ibexa\Contracts\Core\Persistence\Content\Location\Handler as LocationHandler;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Search\Handler as SearchHandler;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

use function sprintf;

final class UserContentWithLocationIndexer
{
    public function __construct(
        private readonly ContentHandler $contentHandler,
        private readonly LocationHandler $locationHandler,
        private readonly SearchHandler $searchHandler,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {}

    public function indexUserContentWithLocation(int $contentId): void
    {
        try {
            $content = $this->contentHandler->load(
                $contentId,
                $this->contentHandler->loadContentInfo($contentId)->currentVersionNo,
            );
        } catch (NotFoundException) {
            $this->logger->info(
                sprintf(
                    '%s: Content #%d is gone, continuing',
                    $this::class,
                    $contentId,
                ),
            );

            return;
        }

        $this->searchHandler->indexContent($content);

        $locations = $this->locationHandler->loadLocationsByContent($contentId);

        foreach ($locations as $location) {
            $this->searchHandler->indexLocation($location);
        }
    }
}
