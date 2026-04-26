<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Tests\Integration\API;

use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\ContentId as ContentIdSortClause;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\ContentId;

class ContentIdCriterionTest extends BaseTestCase
{
    public static function providerForTestFind(): array
    {
        return [
            [
                new LocationQuery([
                    'filter' => new ContentId(Operator::EQ, 14),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                [14],
            ],
            [
                new LocationQuery([
                    'filter' => new ContentId(Operator::IN, [4, 10, 14, 41, 50, 57]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                [4, 10, 14, 41, 50, 57],
            ],
            [
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new ContentId(Operator::IN, [4, 10, 14, 41, 50, 57]),
                        new ContentId(Operator::GT, 14),
                    ]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                [41, 50, 57],
            ],
            [
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new ContentId(Operator::IN, [4, 10, 14, 41, 50, 57]),
                        new ContentId(Operator::GTE, 14),
                    ]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                [14, 41, 50, 57],
            ],
            [
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new ContentId(Operator::IN, [4, 10, 14, 41, 50, 57]),
                        new ContentId(Operator::LT, 41),
                    ]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                [4, 10, 14],
            ],
            [
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new ContentId(Operator::IN, [4, 10, 14, 41, 50, 57]),
                        new ContentId(Operator::LTE, 41),
                    ]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                [4, 10, 14, 41],
            ],
            [
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new ContentId(Operator::IN, [4, 10, 14, 41, 50, 57]),
                        new ContentId(Operator::BETWEEN, [14, 50]),
                    ]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                [14, 41, 50],
            ],
        ];
    }

    /**
     * @dataProvider providerForTestFind
     *
     * @param int[] $expectedIds
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function testFindContent(Query $query, array $expectedIds): void
    {
        $searchService = $this->getSearchService();

        $searchResult = $searchService->findContentInfo($query);

        $this->assertSearchResultContentIds($searchResult, $expectedIds);
    }

    /**
     * @dataProvider providerForTestFind
     *
     * @param int[] $expectedIds
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function testFindLocations(LocationQuery $query, array $expectedIds): void
    {
        $searchService = $this->getSearchService();

        $searchResult = $searchService->findLocations($query);

        $this->assertSearchResultContentIds($searchResult, $expectedIds);
    }
}
