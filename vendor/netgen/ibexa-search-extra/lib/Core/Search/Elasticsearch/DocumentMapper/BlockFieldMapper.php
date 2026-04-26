<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Elasticsearch\DocumentMapper;

use Ibexa\Contracts\Core\Persistence\Content as SPIContent;

abstract class BlockFieldMapper
{
    /**
     * Indicates if the mapper accepts the given $content for mapping.
     */
    abstract public function accept(SPIContent $content): bool;

    /**
     * Maps given $content to an array of search fields.
     *
     * @return \Ibexa\Contracts\Core\Search\Field[]
     */
    abstract public function mapFields(SPIContent $content): array;
}
