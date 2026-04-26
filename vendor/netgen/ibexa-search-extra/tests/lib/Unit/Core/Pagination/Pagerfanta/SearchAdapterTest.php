<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Tests\Unit\Core\Pagination\Pagerfanta;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResultCollection;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Netgen\IbexaSearchExtra\Core\Pagination\Pagerfanta\SearchAdapter;
use PHPUnit\Framework\TestCase;

/**
 * @group pager
 */
class SearchAdapterTest extends TestCase
{
    /**
     * @var \Ibexa\Contracts\Core\Repository\SearchService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $searchService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->searchService = $this->getMockBuilder(SearchService::class)->getMock();
    }

    public function testGetNbResults(): void
    {
        $nbResults = 123;
        $query = new Query(['limit' => 10]);
        $countQuery = clone $query;
        $countQuery->limit = 0;
        $searchResult = new SearchResult(['totalCount' => $nbResults]);

        $this->searchService
            ->expects(self::once())
            ->method('findContent')
            ->with(self::equalTo($countQuery))
            ->willReturn($searchResult);

        $adapter = $this->getAdapter($query);

        self::assertSame($nbResults, $adapter->getNbResults());
        self::assertSame($nbResults, $adapter->getNbResults());
    }

    public function testGetAggregations(): void
    {
        $aggregations = new AggregationResultCollection();
        $query = new Query(['limit' => 10]);
        $countQuery = clone $query;
        $countQuery->limit = 0;
        $searchResult = new SearchResult(['aggregations' => $aggregations, 'totalCount' => 123]);

        $this->searchService
            ->expects(self::once())
            ->method('findContent')
            ->with(self::equalTo($countQuery))
            ->willReturn($searchResult);

        $adapter = $this->getAdapter($query);

        self::assertSame($aggregations, $adapter->getAggregations());
        self::assertSame($aggregations, $adapter->getAggregations());
    }

    public function testMaxScore(): void
    {
        $maxScore = 100.0;
        $query = new Query(['limit' => 10]);
        $countQuery = clone $query;
        $countQuery->limit = 0;
        $searchResult = new SearchResult(['maxScore' => $maxScore, 'totalCount' => 123]);;

        $this->searchService
            ->expects(self::once())
            ->method('findContent')
            ->with(self::equalTo($countQuery))
            ->willReturn($searchResult);

        $adapter = $this->getAdapter($query);

        self::assertSame($maxScore, $adapter->getMaxScore());
        self::assertSame($maxScore, $adapter->getMaxScore());
    }

    public function testTimeIsNotSet(): void
    {
        $this->searchService
            ->expects(self::never())
            ->method('findContent');

        $adapter = $this->getAdapter(new Query());

        self::assertNull($adapter->getTime());
        self::assertNull($adapter->getTime());
    }

    public function testGetSlice(): void
    {
        $offset = 20;
        $limit = 25;
        $nbResults = 123;
        $maxScore = 100.0;
        $time = 256;
        $query = new Query(['offset' => 5, 'limit' => 10]);
        $searchQuery = clone $query;
        $searchQuery->offset = $offset;
        $searchQuery->limit = $limit;
        $searchQuery->performCount = false;

        $hits = [new SearchHit(['valueObject' => 'Content'])];
        $searchResult = new SearchResult([
            'searchHits' => $hits,
            'totalCount' => $nbResults,
            'maxScore' => $maxScore,
            'time' => $time,
        ]);

        $this->searchService
            ->expects(self::once())
            ->method('findContent')
            ->with(self::equalTo($searchQuery))
            ->willReturn($searchResult);

        $adapter = $this->getAdapter($query);
        $slice = $adapter->getSlice($offset, $limit);

        self::assertSame($hits, $slice->getSearchHits());
        self::assertSame($nbResults, $adapter->getNbResults());
        self::assertSame($maxScore, $adapter->getMaxScore());
        self::assertSame($time, $adapter->getTime());
    }

    public function testLocationQuery(): void
    {
        $query = new LocationQuery(['performCount' => false]);

        $this->searchService
            ->expects(self::once())
            ->method('findLocations')
            ->with(self::equalTo($query))
            ->willReturn(new SearchResult(['totalCount' => 123]));

        $adapter = $this->getAdapter($query);
        $adapter->getSlice(0, 25);
    }

    protected function getAdapter(Query $query): SearchAdapter
    {
        return new SearchAdapter($query, $this->searchService);
    }
}
