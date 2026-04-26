<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Solr\SubdocumentMapper\ContentTranslationSubdocumentMapper;

use Ibexa\Contracts\Core\Persistence\Content;
use Netgen\IbexaSearchExtra\Core\Search\Solr\SubdocumentMapper\ContentTranslationSubdocumentMapper;
use function array_merge;

/**
 * Aggregate implementation of Content translation subdocument mapper.
 */
final class Aggregate extends ContentTranslationSubdocumentMapper
{
    /**
     * An array of aggregated subdocument mappers.
     *
     * @var \Netgen\IbexaSearchExtra\Core\Search\Solr\SubdocumentMapper\ContentTranslationSubdocumentMapper[]
     */
    protected array $mappers = [];

    /**
     * @param \Netgen\IbexaSearchExtra\Core\Search\Solr\SubdocumentMapper\ContentTranslationSubdocumentMapper[] $mappers
     */
    public function __construct(array $mappers = [])
    {
        foreach ($mappers as $mapper) {
            $this->addMapper($mapper);
        }
    }

    /**
     * Adds given $mapper to the internal collection.
     *
     * @param \Netgen\IbexaSearchExtra\Core\Search\Solr\SubdocumentMapper\ContentTranslationSubdocumentMapper $mapper
     */
    public function addMapper(ContentTranslationSubdocumentMapper $mapper): void
    {
        $this->mappers[] = $mapper;
    }

    public function accept(Content $content, $languageCode): bool
    {
        return true;
    }

    public function mapDocuments(Content $content, $languageCode): array
    {
        $documentsGrouped = [[]];

        foreach ($this->mappers as $mapper) {
            if ($mapper->accept($content, $languageCode)) {
                $documentsGrouped[] = $mapper->mapDocuments($content, $languageCode);
            }
        }

        return array_merge(...$documentsGrouped);
    }
}
