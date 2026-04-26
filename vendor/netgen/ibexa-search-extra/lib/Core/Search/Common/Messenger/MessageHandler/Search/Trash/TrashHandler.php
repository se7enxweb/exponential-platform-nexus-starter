<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\MessageHandler\Search\Trash;

use Ibexa\Contracts\Core\Search\Handler as SearchHandler;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Trash\Trash;

final class TrashHandler
{
    public function __construct(
        private readonly SearchHandler $searchHandler,
    ) {}

    public function __invoke(Trash $message): void
    {
        if ($message->isContentDeleted) {
            $this->searchHandler->deleteContent($message->contentId);
        }

        $this->searchHandler->deleteLocation(
            $message->locationId,
            $message->contentId,
        );
    }
}
