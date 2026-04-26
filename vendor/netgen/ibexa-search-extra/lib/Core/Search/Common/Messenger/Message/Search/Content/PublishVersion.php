<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Content;

final class PublishVersion
{
    public function __construct(
        public readonly int $contentId,
        public readonly int $versionNo,
    ) {}
}
