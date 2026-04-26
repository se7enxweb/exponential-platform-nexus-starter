<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\PageIndexing;

use Ibexa\Contracts\Core\Persistence\Content\ContentInfo;

abstract class TextExtractor
{
    /**
     * @return array<string, array<int, string>>
     */
    abstract public function extractText(string $source, ContentInfo $contentInfo, string $languageCode): array;
}
