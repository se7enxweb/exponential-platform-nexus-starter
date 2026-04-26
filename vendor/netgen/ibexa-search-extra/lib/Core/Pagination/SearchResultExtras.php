<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Pagination;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResultCollection;
use Netgen\IbexaSearchExtra\API\Values\Content\Search\Suggestion;

/**
 * Defines access to extra information of the search query result.
 */
interface SearchResultExtras
{
    /**
     * Return aggregations for the search query.
     */
    public function getAggregations(): AggregationResultCollection;

    /**
     * Return maximum score for the search query.
     *
     * @return float
     */
    public function getMaxScore(): float;

    /**
     * Return suggestion object for the search query.
     */
    public function getSuggestion(): Suggestion;

    /**
     * Return duration of the search query processing in milliseconds.
     *
     * Note: this will be available only if the query is executed.
     *
     * @return float|int|null
     */
    public function getTime();
}
