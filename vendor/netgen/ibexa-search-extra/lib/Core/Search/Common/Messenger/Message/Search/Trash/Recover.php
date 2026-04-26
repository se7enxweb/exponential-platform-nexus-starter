<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Trash;

final class Recover
{
    public function __construct(
        public readonly int $locationId,
    ) {}
}
