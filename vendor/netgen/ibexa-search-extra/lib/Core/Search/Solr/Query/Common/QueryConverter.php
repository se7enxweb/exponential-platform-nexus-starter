<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Solr\Query\Common;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Solr\Query\AggregationVisitor;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Ibexa\Contracts\Solr\Query\SortClauseVisitor;
use Ibexa\Solr\Query\QueryConverter as BaseQueryConverter;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\FulltextSpellcheck;

use function array_map;
use function implode;
use function json_encode;

use const JSON_THROW_ON_ERROR;

/**
 * Converts the query tree into an array of Solr query parameters.
 */
class QueryConverter extends BaseQueryConverter
{
    protected CriterionVisitor $criterionVisitor;
    protected SortClauseVisitor $sortClauseVisitor;
    private AggregationVisitor $aggregationVisitor;

    public function __construct(
        CriterionVisitor $criterionVisitor,
        SortClauseVisitor $sortClauseVisitor,
        AggregationVisitor $aggregationVisitor
    ) {
        $this->criterionVisitor = $criterionVisitor;
        $this->sortClauseVisitor = $sortClauseVisitor;
        $this->aggregationVisitor = $aggregationVisitor;
    }

    /**
     * @throws \JsonException
     */
    public function convert(Query $query, array $languageSettings = []): array
    {
        $params = [
            'q' => '{!lucene}' . $this->criterionVisitor->visit($query->query),
            'fq' => '{!lucene}' . $this->criterionVisitor->visit($query->filter),
            'sort' => $this->getSortParams($query->sortClauses),
            'start' => $query->offset,
            'rows' => $query->limit,
            'fl' => '*,score,[shard]',
            'wt' => 'json',
        ];

        if (!empty($query->aggregations)) {
            $aggregations = [];

            foreach ($query->aggregations as $aggregation) {
                if ($this->aggregationVisitor->canVisit($aggregation, $languageSettings)) {
                    $aggregations[$aggregation->getName()] = $this->aggregationVisitor->visit(
                        $this->aggregationVisitor,
                        $aggregation,
                        $languageSettings,
                    );
                }
            }

            if (!empty($aggregations)) {
                $params['json.facet'] = json_encode($aggregations, JSON_THROW_ON_ERROR);
            }
        }

        if ($query->query instanceof FulltextSpellcheck) {
            $spellcheckQuery = $query->query->getSpellcheckQuery();

            $params['spellcheck.q'] = $spellcheckQuery->query;
            $params['spellcheck.count'] = $spellcheckQuery->count;

            foreach ($spellcheckQuery->parameters as $key => $value) {
                $params['spellcheck.' . $key] = $value;
            }
        }

        return $params;
    }

    /**
     * Converts an array of sort clause objects to a proper Solr representation.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[] $sortClauses
     *
     * @return string
     */
    private function getSortParams(array $sortClauses): string
    {
        return implode(
            ', ',
            array_map(
                [$this->sortClauseVisitor, 'visit'],
                $sortClauses,
            ),
        );
    }
}
