<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Tests\Integration\API;

use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentId;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalNot;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalOr;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\ContentId as ContentIdSortClause;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\ObjectStateIdentifier;

class ObjectStateIdentifierCriterionTest extends BaseTestCase
{
    public static function providerForTestFind(): array
    {
        return [
            [
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new ContentId([4, 50, 57]),
                        new ObjectStateIdentifier('ibexa_lock', 'not_locked'),
                    ]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                [4, 57],
            ],
            [
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new ContentId([4, 50, 57]),
                        new ObjectStateIdentifier('ibexa_lock', 'locked'),
                    ]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                [50],
            ],
            [
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new ContentId([4, 50, 57]),
                        new LogicalOr([
                            new ObjectStateIdentifier('ibexa_lock', 'locked'),
                            new ObjectStateIdentifier('ibexa_lock', 'not_locked'),
                        ]),
                    ]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                [4, 50, 57],
            ],
            [
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new ContentId([4, 57]),
                        new ObjectStateIdentifier('ibexa_lock', 'locked'),
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
                            new ObjectStateIdentifier('ibexa_lock', 'locked'),
                        ),
                    ]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                [4, 57],
            ],
            [
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new ContentId([4, 50, 57]),
                        new LogicalNot(
                            new ObjectStateIdentifier('ibexa_lock', 'not_locked'),
                        ),
                    ]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                [50],
            ],
        ];
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testPrepareTestFixtures(): void
    {
        $repository = $this->getRepository();
        $contentService = $repository->getContentService();
        $objectStateService = $repository->getObjectStateService();

        $contentInfo = $contentService->loadContentInfo(50);
        $objectStateGroups = $objectStateService->loadObjectStateGroups();

        foreach ($objectStateGroups as $objectStateGroup) {
            if ($objectStateGroup->identifier === 'ibexa_lock') {
                break;
            }
        }

        /** @var \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup $objectStateGroup */
        $objectStates = $objectStateService->loadObjectStates($objectStateGroup);

        foreach ($objectStates as $objectState) {
            if ($objectState->identifier === 'locked') {
                break;
            }
        }

        /* @var \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState $objectState */
        $objectStateService->setContentState($contentInfo, $objectStateGroup, $objectState);
        $this->refreshSearch($repository);

        self::assertTrue(true);
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
        $searchService = $this->getSearchService(false);

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
        $searchService = $this->getSearchService(false);

        $searchResult = $searchService->findLocations($query);

        $this->assertSearchResultContentIds($searchResult, $expectedIds);
    }
}
