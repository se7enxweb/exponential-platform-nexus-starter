<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\Content;

final class DeleteTranslation
{
    public function __construct(
        public readonly int $contentId,
        public readonly string $languageCode,
    ) {}
}
