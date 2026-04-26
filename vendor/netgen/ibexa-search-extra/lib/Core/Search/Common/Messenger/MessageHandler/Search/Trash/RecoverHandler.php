<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\MessageHandler\Search\Trash;

use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Trash\Recover;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\MessageHandler\Search\SubtreeIndexer;

final class RecoverHandler
{
    public function __construct(
        private readonly SubtreeIndexer $subtreeIndexer,
    ) {}

    public function __invoke(Recover $message): void
    {
        $this->subtreeIndexer->indexSubtree($message->locationId);
    }
}
