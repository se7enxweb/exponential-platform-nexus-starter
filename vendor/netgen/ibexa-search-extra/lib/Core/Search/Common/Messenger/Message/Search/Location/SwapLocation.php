<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Location;

final class SwapLocation
{
    public function __construct(
        public readonly int $location1Id,
        public readonly int $content1Id,
        public readonly int $location2Id,
        public readonly int $content2Id,
    ) {}
}
