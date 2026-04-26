<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Elasticsearch\DocumentMapper\BlockTranslationFieldMapper;

use Ibexa\Contracts\Core\Persistence\Content as SPIContent;
use Netgen\IbexaSearchExtra\Core\Search\Elasticsearch\DocumentMapper\BlockTranslationFieldMapper;

class Aggregate extends BlockTranslationFieldMapper
{
    /**
     * An array of aggregated field mappers.
     *
     * @var BlockTranslationFieldMapper[]
     */
    protected array $mappers = [];

    /**
     * @param blockTranslationFieldMapper[] $mappers
     * An array of mappers, sorted by priority
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
    public function addMapper(BlockTranslationFieldMapper $mapper): void
    {
        $this->mappers[] = $mapper;
    }

    public function accept(SPIContent $content, string $languageCode): bool
    {
        return true;
    }

    public function mapFields(SPIContent $content, string $languageCode): array
    {
        $fields = [];

        foreach ($this->mappers as $mapper) {
            if ($mapper->accept($content, $languageCode)) {
                $fields = [...$fields, ...$mapper->mapFields($content, $languageCode)];
            }
        }

        return $fields;
    }
}
