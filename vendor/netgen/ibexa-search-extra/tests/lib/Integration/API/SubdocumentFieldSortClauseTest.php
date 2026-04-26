<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Tests\Integration\API;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentId;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\CustomField;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\ContentId as ContentIdSortClause;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\SubdocumentQuery;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\SortClause\SubdocumentField;

/**
 * @group subdocument
 *
 * @see \Netgen\IbexaSearchExtra\API\Values\Content\Query\SortClause\SubdocumentField
 */
class SubdocumentFieldSortClauseTest extends BaseTestCase
{
    public static function providerForTestSort(): array
    {
        $documentTypeIdentifier = 'test_sort_content_subdocument';

        return [
            [
                new Query([
                    'filter' => new ContentId([12, 42, 59, 4]),
                    'sortClauses' => [
                        new SubdocumentField(
                            'price_i',
                            $documentTypeIdentifier,
                            SubdocumentField::ScoringModeMaximum,
                            Query::SORT_ASC,
                        ),
                    ],
                ]),
                [4, 59, 12, 42],
            ],
            [
                new Query([
                    'filter' => new ContentId([12, 42, 59, 4]),
                    'sortClauses' => [
                        new SubdocumentField(
                            'price_i',
                            $documentTypeIdentifier,
                            SubdocumentField::ScoringModeMaximum,
                            Query::SORT_DESC,
                        ),
                    ],
                ]),
                [42, 12, 59, 4],
            ],
            [
                new Query([
                    'filter' => new ContentId([12, 42, 59, 4]),
                    'sortClauses' => [
                        new SubdocumentField(
                            'price_i',
                            $documentTypeIdentifier,
                            SubdocumentField::ScoringModeMinimum,
                            Query::SORT_ASC,
                        ),
                    ],
                ]),
                [4, 42, 12, 59],
            ],
            [
                new Query([
                    'filter' => new ContentId([12, 42, 59, 4]),
                    'sortClauses' => [
                        new SubdocumentField(
                            'price_i',
                            $documentTypeIdentifier,
                            SubdocumentField::ScoringModeMinimum,
                            Query::SORT_DESC,
                        ),
                    ],
                ]),
                [59, 12, 42, 4],
            ],
            [
                new Query([
                    'filter' => new ContentId([12, 42, 59, 4]),
                    'sortClauses' => [
                        new SubdocumentField(
                            'price_i',
                            $documentTypeIdentifier,
                            SubdocumentField::ScoringModeTotal,
                            Query::SORT_ASC,
                        ),
                    ],
                ]),
                [4, 12, 59, 42],
            ],
            [
                new Query([
                    'filter' => new ContentId([12, 42, 59, 4]),
                    'sortClauses' => [
                        new SubdocumentField(
                            'price_i',
                            $documentTypeIdentifier,
                            SubdocumentField::ScoringModeTotal,
                            Query::SORT_DESC,
                        ),
                    ],
                ]),
                [42, 59, 12, 4],
            ],
            [
                new Query([
                    'filter' => new ContentId([12, 42, 59, 4]),
                    'sortClauses' => [
                        new SubdocumentField(
                            'price_i',
                            $documentTypeIdentifier,
                            SubdocumentField::ScoringModeAverage,
                            Query::SORT_ASC,
                        ),
                    ],
                ]),
                [4, 12, 59, 42],
            ],
            [
                new Query([
                    'filter' => new ContentId([12, 42, 59, 4]),
                    'sortClauses' => [
                        new SubdocumentField(
                            'price_i',
                            $documentTypeIdentifier,
                            SubdocumentField::ScoringModeAverage,
                            Query::SORT_DESC,
                        ),
                    ],
                ]),
                [42, 59, 12, 4],
            ],
            [
                new Query([
                    'filter' => new ContentId([12, 42, 59, 4]),
                    'sortClauses' => [
                        new SubdocumentField(
                            'price_i',
                            $documentTypeIdentifier,
                            SubdocumentField::ScoringModeNone,
                            Query::SORT_ASC,
                        ),
                        new ContentIdSortClause(Query::SORT_ASC),
                    ],
                ]),
                [4, 12, 42, 59],
            ],
            [
                new Query([
                    'filter' => new ContentId([12, 42, 59, 4]),
                    'sortClauses' => [
                        new SubdocumentField(
                            'price_i',
                            $documentTypeIdentifier,
                            SubdocumentField::ScoringModeNone,
                            Query::SORT_DESC,
                        ),
                        new ContentIdSortClause(Query::SORT_DESC),
                    ],
                ]),
                [59, 42, 12, 4],
            ],
        ];
    }

    /**
     * @dataProvider providerForTestSort
     *
     * @param int[] $expectedIds
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function testSortContent(Query $query, array $expectedIds): void
    {
        $searchService = $this->getSearchService();

        $searchResult = $searchService->findContentInfo($query);

        $this->assertSearchResultContentIds($searchResult, $expectedIds);
    }

    public static function providerForTestFilteredSort(): array
    {
        $documentTypeIdentifier = 'test_sort_content_subdocument';
        $filter = new SubdocumentQuery(
            'test_sort_content_subdocument',
            new CustomField('price_i', Operator::LT, 20),
        );

        return [
            [
                new Query([
                    'filter' => new ContentId([12, 42, 59, 4]),
                    'sortClauses' => [
                        new SubdocumentField(
                            'price_i',
                            $documentTypeIdentifier,
                            SubdocumentField::ScoringModeMaximum,
                            Query::SORT_ASC,
                            $filter,
                        ),
                    ],
                ]),
                [4, 42, 12, 59],
            ],
            [
                new Query([
                    'filter' => new ContentId([12, 42, 59, 4]),
                    'sortClauses' => [
                        new SubdocumentField(
                            'price_i',
                            $documentTypeIdentifier,
                            SubdocumentField::ScoringModeMaximum,
                            Query::SORT_DESC,
                            $filter,
                        ),
                    ],
                ]),
                [59, 12, 42, 4],
            ],
            [
                new Query([
                    'filter' => new ContentId([12, 42, 59, 4]),
                    'sortClauses' => [
                        new SubdocumentField(
                            'price_i',
                            $documentTypeIdentifier,
                            SubdocumentField::ScoringModeMinimum,
                            Query::SORT_ASC,
                            $filter,
                        ),
                    ],
                ]),
                [4, 42, 12, 59],
            ],
            [
                new Query([
                    'filter' => new ContentId([12, 42, 59, 4]),
                    'sortClauses' => [
                        new SubdocumentField(
                            'price_i',
                            $documentTypeIdentifier,
                            SubdocumentField::ScoringModeMinimum,
                            Query::SORT_DESC,
                            $filter,
                        ),
                    ],
                ]),
                [59, 12, 42, 4],
            ],
            [
                new Query([
                    'filter' => new ContentId([12, 42, 59, 4]),
                    'sortClauses' => [
                        new SubdocumentField(
                            'price_i',
                            $documentTypeIdentifier,
                            SubdocumentField::ScoringModeTotal,
                            Query::SORT_ASC,
                            $filter,
                        ),
                    ],
                ]),
                [4, 42, 12, 59],
            ],
            [
                new Query([
                    'filter' => new ContentId([12, 42, 59, 4]),
                    'sortClauses' => [
                        new SubdocumentField(
                            'price_i',
                            $documentTypeIdentifier,
                            SubdocumentField::ScoringModeTotal,
                            Query::SORT_DESC,
                            $filter,
                        ),
                    ],
                ]),
                [59, 12, 42, 4],
            ],
            [
                new Query([
                    'filter' => new ContentId([12, 42, 59, 4]),
                    'sortClauses' => [
                        new SubdocumentField(
                            'price_i',
                            $documentTypeIdentifier,
                            SubdocumentField::ScoringModeAverage,
                            Query::SORT_ASC,
                            $filter,
                        ),
                    ],
                ]),
                [4, 42, 12, 59],
            ],
            [
                new Query([
                    'filter' => new ContentId([12, 42, 59, 4]),
                    'sortClauses' => [
                        new SubdocumentField(
                            'price_i',
                            $documentTypeIdentifier,
                            SubdocumentField::ScoringModeAverage,
                            Query::SORT_DESC,
                            $filter,
                        ),
                    ],
                ]),
                [59, 12, 42, 4],
            ],
            [
                new Query([
                    'filter' => new ContentId([12, 42, 59, 4]),
                    'sortClauses' => [
                        new SubdocumentField(
                            'price_i',
                            $documentTypeIdentifier,
                            SubdocumentField::ScoringModeNone,
                            Query::SORT_ASC,
                            $filter,
                        ),
                        new ContentIdSortClause(Query::SORT_ASC),
                    ],
                ]),
                [4, 12, 42, 59],
            ],
            [
                new Query([
                    'filter' => new ContentId([12, 42, 59, 4]),
                    'sortClauses' => [
                        new SubdocumentField(
                            'price_i',
                            $documentTypeIdentifier,
                            SubdocumentField::ScoringModeNone,
                            Query::SORT_DESC,
                            $filter,
                        ),
                        new ContentIdSortClause(Query::SORT_DESC),
                    ],
                ]),
                [59, 42, 12, 4],
            ],
        ];
    }

    /**
     * @dataProvider providerForTestFilteredSort
     *
     * @param int[] $expectedIds
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function testFilteredSortContent(Query $query, array $expectedIds): void
    {
        $searchService = $this->getSearchService();

        $searchResult = $searchService->findContentInfo($query);

        $this->assertSearchResultContentIds($searchResult, $expectedIds);
    }
}
