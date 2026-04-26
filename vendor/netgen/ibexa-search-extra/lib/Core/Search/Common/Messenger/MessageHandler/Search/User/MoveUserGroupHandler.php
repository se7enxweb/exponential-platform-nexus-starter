<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\MessageHandler\Search\User;

use Ibexa\Contracts\Core\Persistence\Content\Handler as ContentHandler;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\User\MoveUserGroup;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\MessageHandler\Search\SubtreeIndexer;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

use function sprintf;

final class MoveUserGroupHandler
{
    public function __construct(
        private readonly ContentHandler $contentHandler,
        private readonly SubtreeIndexer $subtreeIndexer,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {}

    public function __invoke(MoveUserGroup $message): void
    {
        try {
            $userGroupContentInfo = $this->contentHandler->loadContentInfo(
                $message->contentId,
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

        $this->subtreeIndexer->indexSubtree(
            $userGroupContentInfo->mainLocationId,
        );
    }
}
