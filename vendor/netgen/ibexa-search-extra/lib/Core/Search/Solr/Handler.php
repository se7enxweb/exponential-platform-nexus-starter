<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Solr;

use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Contracts\Solr\DocumentMapper;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Solr\Handler as BaseHandler;
use stdClass;

class Handler extends BaseHandler
{
    public function findContent(Query $query, array $languageFilter = []): SearchResult
    {
        $query = clone $query;
        $query->filter = $query->filter ?: new Criterion\MatchAll();
        $query->query = $query->query ?: new Criterion\MatchAll();

        $this->coreFilter->apply(
            $query,
            $languageFilter,
            DocumentMapper::DOCUMENT_TYPE_IDENTIFIER_CONTENT,
        );

        return $this->contentResultExtractor->extract(
            $this->gateway->findContent($query, $languageFilter),
            $query->aggregations,
            $languageFilter,
            $query->spellcheck,
            $query,
        );
    }

    public function findLocations(LocationQuery $query, array $languageFilter = []): SearchResult
    {
        $query = clone $query;
        $query->query = $query->query ?: new Criterion\MatchAll();

        $this->coreFilter->apply(
            $query,
            $languageFilter,
            DocumentMapper::DOCUMENT_TYPE_IDENTIFIER_LOCATION,
        );

        return $this->locationResultExtractor->extract(
            $this->gateway->findLocations($query, $languageFilter),
            $query->aggregations,
            $languageFilter,
            $query->spellcheck,
            $query,
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidCriterionArgumentException
     */
    protected function deleteAllItemsWithoutAdditionalLocation($locationId): void
    {
        $query = $this->prepareQuery(self::SOLR_MAX_QUERY_LIMIT);
        $query->filter = new Criterion\LogicalAnd([
            $this->allItemsWithinLocation($locationId),
            new Criterion\LogicalNot($this->allItemsWithinLocationWithAdditionalLocation($locationId)),
        ]);

        $contentIds = $this->extractContentIds(
            $this->gateway->searchAllEndpoints($query),
        );

        $contentDocumentIds = [];

        foreach ($contentIds as $id) {
            $contentDocumentIds[] = $this->mapper->generateContentDocumentId($id) . '*';
        }

        foreach (array_chunk(array_unique($contentDocumentIds), self::SOLR_BULK_REMOVE_LIMIT) as $ids) {
            $query = '_root_:(' . implode(' OR ', $ids) . ')';
            $this->gateway->deleteByQuery($query);
        }
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidCriterionArgumentException
     */
    protected function updateAllElementsWithAdditionalLocation($locationId): void
    {
        $query = $this->prepareQuery();
        $query->filter = new Criterion\LogicalAnd([
            $this->allItemsWithinLocation($locationId),
            $this->allItemsWithinLocationWithAdditionalLocation($locationId),
        ]);

        $contentIds = $this->extractContentIds(
            $this->gateway->searchAllEndpoints($query),
        );

        $contentItems = [];
        foreach ($contentIds as $contentId) {
            try {
                $contentInfo = $this->contentHandler->loadContentInfo($contentId);
            } catch (NotFoundException) {
                continue;
            }

            $contentItems[] = $this->contentHandler->load(
                $contentInfo->id,
                $contentInfo->currentVersionNo,
            );
        }

        $this->bulkIndexContent($contentItems);
    }

    /**
     * @return int[]
     */
    private function extractContentIds(stdClass $data): array
    {
        $ids = [];

        foreach ($data->response->docs as $doc) {
            $ids[] = (int) $doc->content_id_id;
        }

        return $ids;
    }
}
