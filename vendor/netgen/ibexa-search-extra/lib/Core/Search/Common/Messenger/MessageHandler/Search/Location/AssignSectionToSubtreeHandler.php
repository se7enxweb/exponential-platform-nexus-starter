<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\MessageHandler\Search\Location;

use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Location\AssignSectionToSubtree;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\MessageHandler\Search\SubtreeIndexer;

final class AssignSectionToSubtreeHandler
{
    public function __construct(
        private readonly SubtreeIndexer $subtreeIndexer,
    ) {}

    public function __invoke(AssignSectionToSubtree $message): void
    {
        $this->subtreeIndexer->indexSubtree($message->locationId);
    }
}
