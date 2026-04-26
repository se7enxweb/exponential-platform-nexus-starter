<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\MessageHandler\Search\Content;

use Ibexa\Contracts\Core\Search\Handler as SearchHandler;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Content\DeleteContent;

final class DeleteContentHandler
{
    public function __construct(
        private readonly SearchHandler $searchHandler,
    ) {}

    public function __invoke(DeleteContent $message): void
    {
        $this->searchHandler->deleteContent($message->contentId);

        foreach ($message->locationIds as $locationId) {
            $this->searchHandler->deleteLocation(
                $locationId,
                $message->contentId,
            );
        }
    }
}
