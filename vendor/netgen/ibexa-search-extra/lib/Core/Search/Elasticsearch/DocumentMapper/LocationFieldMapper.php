<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Elasticsearch\DocumentMapper;

use Ibexa\Contracts\Core\Persistence\Content\Location as SPILocation;

abstract class LocationFieldMapper
{
    /**
     * Indicates if the mapper accepts the given $content for mapping.
     */
    abstract public function accept(SPILocation $location): bool;

    /**
     * Maps given $content to an array of search fields.
     *
     * @return \Ibexa\Contracts\Core\Search\Field[]
     */
    abstract public function mapFields(SPILocation $location): array;
}
