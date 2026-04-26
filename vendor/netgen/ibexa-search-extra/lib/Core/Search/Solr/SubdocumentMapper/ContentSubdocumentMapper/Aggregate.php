<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Solr\SubdocumentMapper\ContentSubdocumentMapper;

use Ibexa\Contracts\Core\Persistence\Content;
use Netgen\IbexaSearchExtra\Core\Search\Solr\SubdocumentMapper\ContentSubdocumentMapper;
use function array_merge;

/**
 * Aggregate implementation of Content subdocument mapper.
 */
final class Aggregate extends ContentSubdocumentMapper
{
    /**
     * An array of aggregated subdocument mappers.
     *
     * @var \Netgen\IbexaSearchExtra\Core\Search\Solr\SubdocumentMapper\ContentSubdocumentMapper[]
     */
    protected array $mappers = [];

    /**
     * @param \Netgen\IbexaSearchExtra\Core\Search\Solr\SubdocumentMapper\ContentSubdocumentMapper[] $mappers
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
     * @param \Netgen\IbexaSearchExtra\Core\Search\Solr\SubdocumentMapper\ContentSubdocumentMapper $mapper
     */
    public function addMapper(ContentSubdocumentMapper $mapper): void
    {
        $this->mappers[] = $mapper;
    }

    public function accept(Content $content): bool
    {
        return true;
    }

    public function mapDocuments(Content $content): array
    {
        $documentsGrouped = [[]];

        foreach ($this->mappers as $mapper) {
            if ($mapper->accept($content)) {
                $documentsGrouped[] = $mapper->mapDocuments($content);
            }
        }

        return array_merge(...$documentsGrouped);
    }
}
