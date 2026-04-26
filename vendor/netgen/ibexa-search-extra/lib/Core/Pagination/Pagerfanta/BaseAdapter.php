<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Pagination\Pagerfanta;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResultCollection;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Netgen\IbexaSearchExtra\API\Values\Content\Search\SearchResult as ExtraSearchResult;
use Netgen\IbexaSearchExtra\API\Values\Content\Search\Suggestion;
use Netgen\IbexaSearchExtra\Core\Pagination\SearchResultExtras;
use Pagerfanta\Adapter\AdapterInterface;

/**
 * Base search adapter.
 */
abstract class BaseAdapter implements AdapterInterface, SearchResultExtras
{
    private ?int $nbResults = null;
    private ?AggregationResultCollection $aggregations = null;
    private ?float $maxScore = null;
    private ?Suggestion $suggestion = null;

    /** @var ?int|?float */
    private $time;

    private Query $query;
    private bool $isExtraInfoInitialized = false;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    public function getNbResults(): int
    {
        $this->initializeExtraInfo();

        return $this->nbResults;
    }

    public function getAggregations(): AggregationResultCollection
    {
        $this->initializeExtraInfo();

        return $this->aggregations;
    }

    public function getMaxScore(): float
    {
        $this->initializeExtraInfo();

        return $this->maxScore ?? 0.0;
    }

    public function getSuggestion(): Suggestion
    {
        $this->initializeExtraInfo();

        return $this->suggestion;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function getSlice($offset, $length): Slice
    {
        $query = clone $this->query;
        $query->offset = $offset;
        $query->limit = $length;
        $query->performCount = false;

        $searchResult = $this->executeQuery($query);

        $this->time = $searchResult->time;

        if (!$this->isExtraInfoInitialized && $searchResult->totalCount !== null) {
            $this->setExtraInfo($searchResult);
        }

        return new Slice($searchResult->searchHits);
    }

    /**
     * Execute the given $query and return the SearchResult instance.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query $query
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult
     */
    abstract protected function executeQuery(Query $query): SearchResult;

    private function initializeExtraInfo(): void
    {
        if ($this->isExtraInfoInitialized) {
            return;
        }

        $query = clone $this->query;
        $query->limit = 0;
        $searchResult = $this->executeQuery($query);

        $this->setExtraInfo($searchResult);
    }

    private function setExtraInfo(SearchResult $searchResult): void
    {
        $this->aggregations = $searchResult->aggregations;
        $this->maxScore = $searchResult->maxScore;
        $this->nbResults = $searchResult->totalCount;
        $this->suggestion = new Suggestion([]);

        if ($searchResult instanceof ExtraSearchResult && $searchResult->suggestion instanceof Suggestion) {
            $this->suggestion = $searchResult->suggestion;
        }

        $this->isExtraInfoInitialized = true;
    }
}
