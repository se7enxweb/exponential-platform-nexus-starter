<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\MessageHandler\Search\User;

use Ibexa\Contracts\Core\Search\Handler as SearchHandler;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\User\DeleteUserGroup;

final class DeleteUserGroupHandler
{
    public function __construct(
        private readonly SearchHandler $searchHandler,
    ) {}

    public function __invoke(DeleteUserGroup $message): void
    {
        $this->searchHandler->deleteContent($message->contentId);

        foreach ($message->locationIds as $locationId) {
            $this->searchHandler->deleteLocation($locationId, $message->contentId);
        }
    }
}
