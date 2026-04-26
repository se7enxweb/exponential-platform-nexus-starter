<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Elasticsearch\DocumentMapper;

use Ibexa\Contracts\Core\Persistence\Content as SPIContent;

abstract class BlockTranslationFieldMapper
{
    /**
     * Indicates if the mapper accepts the given $content for mapping.
     */
    abstract public function accept(SPIContent $content, string $languageCode): bool;

    /**
     * Maps given $content to an array of search fields.
     *
     * @return \Ibexa\Contracts\Core\Search\Field[]
     */
    abstract public function mapFields(SPIContent $content, string $languageCode): array;
}
