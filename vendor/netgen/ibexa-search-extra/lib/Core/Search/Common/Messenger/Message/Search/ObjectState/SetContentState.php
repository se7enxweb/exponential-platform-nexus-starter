<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\ObjectState;

final class SetContentState
{
    public function __construct(
        public readonly int $contentId,
    ) {}
}
