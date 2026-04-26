<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\MessageHandler\Search\Location;

use Ibexa\Contracts\Core\Search\Handler as SearchHandler;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Location\DeleteLocation;

final class DeleteLocationHandler
{
    public function __construct(
        private readonly SearchHandler $searchHandler,
    ) {}

    public function __invoke(DeleteLocation $message): void
    {
        $this->searchHandler->deleteLocation(
            $message->locationId,
            $message->contentId,
        );
    }
}
