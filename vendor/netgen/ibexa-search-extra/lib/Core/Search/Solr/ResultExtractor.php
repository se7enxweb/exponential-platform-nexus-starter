<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Solr;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Spellcheck;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Solr\ResultExtractor as BaseResultExtractor;
use Netgen\IbexaSearchExtra\API\Values\Content\Search\LocationQuery as ExtraLocationQuery;
use Netgen\IbexaSearchExtra\API\Values\Content\Search\Query as ExtraQuery;
use Netgen\IbexaSearchExtra\API\Values\Content\Search\SearchHit;
use stdClass;

use function get_object_vars;
use function property_exists;

/**
 * This DocumentMapper implementation adds support for extra field.
 */
abstract class ResultExtractor extends BaseResultExtractor
{
    public function extract(
        $data,
        array $aggregations = [],
        array $languageFilter = [],
        ?Spellcheck $spellcheck = null,
        ?Query $query = null,
    ): SearchResult {
        $searchResult = $this->extractSearchResult(
            $data,
            $aggregations,
            $languageFilter,
            $spellcheck,
        );

        foreach ($searchResult->searchHits as $key => $searchHit) {
            $searchResult->searchHits[$key] = new SearchHit(get_object_vars($searchHit));
            $searchResult->searchHits[$key]->extraFields = [];

            if (($query instanceof ExtraQuery || $query instanceof ExtraLocationQuery) && !empty($query->extraFields)) {
                $searchResult->searchHits[$key]->extraFields = $this->extractExtraFields(
                    $data,
                    $searchResult->searchHits[$key],
                    $query->extraFields,
                );
            }
        }

        return $searchResult;
    }

    /**
     * Extract the base search result.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation[] $aggregations
     * @param array{languages?: string[], languageCode?: string, useAlwaysAvailable?: bool} $languageFilter
     */
    abstract protected function extractSearchResult(
        stdClass $data,
        array $aggregations = [],
        array $languageFilter = [],
        ?Spellcheck $spellcheck = null,
    ): SearchResult;

    /**
     * @param string[] $extraFields
     */
    private function extractExtraFields(stdClass $data, SearchHit $searchHit, array $extraFields): array
    {
        $extractedExtraFields = [];

        foreach ($data->response->docs as $doc) {
            if (
                ($doc->document_type_id === 'content' && (int) $doc->content_id_id === $searchHit->valueObject->id)
                || ($doc->document_type_id === 'location' && (int) $doc->location_id === $searchHit->valueObject->id)
            ) {
                foreach ($extraFields as $extraField) {
                    if (property_exists($doc, $extraField)) {
                        $extractedExtraFields[$extraField] = $doc->{$extraField};
                    }
                }
            }
        }

        return $extractedExtraFields;
    }
}
