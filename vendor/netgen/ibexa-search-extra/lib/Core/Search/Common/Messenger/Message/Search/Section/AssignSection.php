<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Section;

final class AssignSection
{
    public function __construct(
        public readonly int $contentId,
    ) {}
}
