<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\MessageHandler\Search\Section;

use Ibexa\Contracts\Core\Persistence\Content\Handler as ContentHandler;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Search\Handler as SearchHandler;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Section\AssignSection;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

use function sprintf;

final class AssignSectionHandler
{
    public function __construct(
        private readonly ContentHandler $contentHandler,
        private readonly SearchHandler $searchHandler,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {}

    public function __invoke(AssignSection $message): void
    {
        try {
            $content = $this->contentHandler->load(
                $message->contentId,
                $this->contentHandler->loadContentInfo($message->contentId)->currentVersionNo,
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

        $this->searchHandler->indexContent($content);
    }
}
