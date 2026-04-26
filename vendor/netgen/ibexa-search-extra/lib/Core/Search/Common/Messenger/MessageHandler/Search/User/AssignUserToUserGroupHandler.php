<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\MessageHandler\Search\User;

use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\User\AssignUserToUserGroup;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\MessageHandler\Search\UserContentWithLocationIndexer;

final class AssignUserToUserGroupHandler
{
    public function __construct(
        private readonly UserContentWithLocationIndexer $userContentWithLocationIndexer,
    ) {}

    public function __invoke(AssignUserToUserGroup $message): void
    {
        $this->userContentWithLocationIndexer->indexUserContentWithLocation(
            $message->contentId,
        );
    }
}
