<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Location;

final class CreateLocation
{
    public function __construct(
        public readonly int $locationId,
        public readonly int $contentId,
    ) {}
}
