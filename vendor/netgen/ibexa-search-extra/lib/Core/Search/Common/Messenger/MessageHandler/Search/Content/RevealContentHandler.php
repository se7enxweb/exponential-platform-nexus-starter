<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\MessageHandler\Search\Content;

use Ibexa\Contracts\Core\Persistence\Content\Location\Handler as LocationHandler;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Content\RevealContent;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\MessageHandler\Search\SubtreeIndexer;

final class RevealContentHandler
{
    public function __construct(
        private readonly LocationHandler $locationHandler,
        private readonly SubtreeIndexer $subtreeIndexer,
    ) {}

    public function __invoke(RevealContent $message): void
    {
        $locations = $this->locationHandler->loadLocationsByContent(
            $message->contentId,
        );

        foreach ($locations as $location) {
            $this->subtreeIndexer->indexSubtree($location->id);
        }
    }
}
