<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\MessageHandler\Search\Content;

use Ibexa\Contracts\Core\Persistence\Content\Handler as ContentHandler;
use Ibexa\Contracts\Core\Persistence\Content\Location\Handler as LocationHandler;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Search\VersatileHandler as SearchHandler;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Content\CopyContent;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

use function sprintf;

final class CopyContentHandler
{
    public function __construct(
        private readonly ContentHandler $contentHandler,
        private readonly LocationHandler $locationHandler,
        private readonly SearchHandler $searchHandler,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {}

    public function __invoke(CopyContent $message): void
    {
        try {
            $content = $this->contentHandler->load(
                $message->contentId,
                $message->versionNo,
            );
        } catch (NotFoundException) {
            $this->logger->info(
                sprintf(
                    '%s: Content #%d in version %d is gone, aborting',
                    $this::class,
                    $message->contentId,
                    $message->versionNo,
                ),
            );

            return;
        }

        $this->searchHandler->indexContent($content);

        $locations = $this->locationHandler->loadLocationsByContent(
            $message->contentId,
        );

        foreach ($locations as $location) {
            $this->searchHandler->indexLocation($location);
        }
    }
}
