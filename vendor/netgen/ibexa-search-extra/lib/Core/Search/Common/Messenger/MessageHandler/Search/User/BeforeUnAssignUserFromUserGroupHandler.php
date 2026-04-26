<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\MessageHandler\Search\User;

use Ibexa\Contracts\Core\Persistence\Content\Location\Handler as LocationHandler;
use Ibexa\Contracts\Core\Search\Handler as SearchHandler;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\User\BeforeUnAssignUserFromUserGroup;

final class BeforeUnAssignUserFromUserGroupHandler
{
    public function __construct(
        private readonly LocationHandler $locationHandler,
        private readonly SearchHandler $searchHandler,
    ) {}

    public function __invoke(BeforeUnAssignUserFromUserGroup $message): void
    {
        $locations = $this->locationHandler->loadLocationsByContent(
            $message->contentId,
        );

        foreach ($locations as $location) {
            $this->searchHandler->deleteLocation($location->id, $message->contentId);
        }
    }
}
