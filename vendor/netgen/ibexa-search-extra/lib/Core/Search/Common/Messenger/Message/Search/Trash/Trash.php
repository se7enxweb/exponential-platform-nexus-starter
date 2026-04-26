<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Trash;

final class Trash
{
    public function __construct(
        public readonly int $locationId,
        public readonly int $parentLocationId,
        public readonly int $contentId,
        public readonly bool $isContentDeleted,
    ) {}
}
