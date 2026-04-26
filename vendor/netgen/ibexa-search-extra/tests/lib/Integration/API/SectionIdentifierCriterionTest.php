<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Tests\Integration\API;

use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentId;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalNot;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\ContentId as ContentIdSortClause;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\SectionIdentifier;

class SectionIdentifierCriterionTest extends BaseTestCase
{
    public static function providerForTestFind(): array
    {
        return [
            [
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new ContentId([4, 50, 57]),
                        new SectionIdentifier('standard'),
                    ]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                [57],
            ],
            [
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new ContentId([4, 50, 57]),
                        new SectionIdentifier('users'),
                    ]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                [4],
            ],
            [
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new ContentId([4, 50, 57]),
                        new SectionIdentifier(['standard', 'users']),
                    ]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                [4, 57],
            ],
            [
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new ContentId([4, 50, 57]),
                        new SectionIdentifier('setup'),
                    ]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                [],
            ],
            [
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new ContentId([4, 50, 57]),
                        new LogicalNot(
                            new SectionIdentifier('standard'),
                        ),
                    ]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                [4, 50],
            ],
            [
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new ContentId([4, 50, 57]),
                        new LogicalNot(
                            new SectionIdentifier('users'),
                        ),
                    ]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                [50, 57],
            ],
            [
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new ContentId([4, 50, 57]),
                        new LogicalNot(
                            new SectionIdentifier(['standard', 'users']),
                        ),
                    ]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                [50],
            ],
            [
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new ContentId([4, 50, 57]),
                        new LogicalNot(
                            new SectionIdentifier('setup'),
                        ),
                    ]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                [4, 50, 57],
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
