<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Location;

final class HideLocation
{
    public function __construct(
        public readonly int $locationId,
    ) {}
}
