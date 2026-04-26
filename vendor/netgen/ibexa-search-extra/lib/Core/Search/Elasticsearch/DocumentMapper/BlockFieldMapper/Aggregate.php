<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Elasticsearch\DocumentMapper\BlockFieldMapper;

use Ibexa\Contracts\Core\Persistence\Content as SPIContent;
use Netgen\IbexaSearchExtra\Core\Search\Elasticsearch\DocumentMapper\BlockFieldMapper;

class Aggregate extends BlockFieldMapper
{
    /**
     * An array of aggregated field mappers.
     *
     * @var BlockFieldMapper[]
     */
    protected array $mappers = [];

    /**
     * @param blockFieldMapper[] $mappers
     * An array of mappers
     */
    public function __construct(array $mappers = [])
    {
        foreach ($mappers as $mapper) {
            $this->addMapper($mapper);
        }
    }

    /**
     * Adds the given $mapper to the internal array.
     */
    public function addMapper(BlockFieldMapper $mapper): void
    {
        $this->mappers[] = $mapper;
    }

    public function accept(SPIContent $content): bool
    {
        return true;
    }

    public function mapFields(SPIContent $content): array
    {
        $fields = [];

        foreach ($this->mappers as $mapper) {
            if ($mapper->accept($content)) {
                $fields = [...$fields, ...$mapper->mapFields($content)];
            }
        }

        return $fields;
    }
}
