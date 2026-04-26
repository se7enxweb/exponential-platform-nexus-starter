<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Tests\Integration\API;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentId;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\CustomField;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalNot;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\ContentId as ContentIdSortClause;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\SubdocumentQuery;

/**
 * @see \Netgen\IbexaSearchExtra\Tests\Integration\Implementation\Solr\SubdocumentMapper\TestContentSubdocumentMapper
 * @see \Netgen\IbexaSearchExtra\Tests\Integration\Implementation\Solr\SubdocumentMapper\TestContentTranslationSubdocumentMapper
 */
class SubdocumentQueryCriterionTest extends BaseTestCase
{
    public static function providerForTestFind(): array
    {
        return [
            [
                new Query([
                    'filter' => new LogicalAnd([
                        new ContentId([4, 12, 13, 42, 59]),
                        new SubdocumentQuery(
                            'test_content_subdocument',
                            new LogicalAnd([
                                new CustomField('visible_b', Operator::EQ, true),
                                new CustomField('price_i', Operator::EQ, 40),
                            ]),
                        ),
                    ]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                [],
                [12],
            ],
            [
                new Query([
                    'filter' => new LogicalAnd([
                        new ContentId([4, 12, 13, 42, 59]),
                        new SubdocumentQuery(
                            'test_content_subdocument',
                            new LogicalNot(
                                new LogicalAnd([
                                    new CustomField('visible_b', Operator::EQ, true),
                                    new CustomField('price_i', Operator::EQ, 40),
                                ]),
                            ),
                        ),
                    ]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                [],
                [12, 42],
            ],
            [
                new Query([
                    'filter' => new LogicalAnd([
                        new ContentId([4, 12, 13, 42, 59]),
                        new SubdocumentQuery(
                            'test_content_subdocument',
                            new LogicalNot(
                                new LogicalAnd([
                                    new CustomField('visible_b', Operator::EQ, false),
                                    new CustomField('price_i', Operator::EQ, 50),
                                ]),
                            ),
                        ),
                    ]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                [],
                [12, 42],
            ],
            [
                new Query([
                    'filter' => new LogicalAnd([
                        new ContentId([4, 12, 13, 42, 59]),
                        new LogicalNot(
                            new SubdocumentQuery(
                                'test_content_subdocument',
                                new LogicalAnd([
                                    new CustomField('visible_b', Operator::EQ, true),
                                    new CustomField('price_i', Operator::EQ, 40),
                                ]),
                            ),
                        ),
                    ]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                [],
                [4, 13, 42, 59],
            ],
            [
                new Query([
                    'filter' => new LogicalAnd([
                        new ContentId([4, 12, 13, 42, 59]),
                        new LogicalNot(
                            new SubdocumentQuery(
                                'test_content_subdocument',
                                new LogicalAnd([
                                    new CustomField('visible_b', Operator::EQ, false),
                                    new CustomField('price_i', Operator::EQ, 50),
                                ]),
                            ),
                        ),
                    ]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                [],
                [4, 13, 59],
            ],
            [
                new Query([
                    'filter' => new LogicalAnd([
                        new ContentId([4, 12, 13, 42, 59]),
                        new SubdocumentQuery(
                            'test_content_translation_subdocument',
                            new CustomField('visible_b', Operator::EQ, true),
                        ),
                    ]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                [],
                [],
            ],
            [
                new Query([
                    'filter' => new LogicalAnd([
                        new ContentId([4, 12, 13, 42, 59]),
                        new SubdocumentQuery(
                            'test_content_translation_subdocument',
                            new CustomField('visible_b', Operator::EQ, true),
                        ),
                    ]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                ['languages' => ['ger-DE']],
                [4, 59],
            ],
            [
                new Query([
                    'filter' => new LogicalAnd([
                        new ContentId([4, 12, 13, 42, 59]),
                        new SubdocumentQuery(
                            'test_content_translation_subdocument',
                            new LogicalAnd([
                                new CustomField('visible_b', Operator::EQ, true),
                                new CustomField('price_i', Operator::EQ, 40),
                            ]),
                        ),
                    ]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                ['languages' => ['ger-DE']],
                [59],
            ],
            [
                new Query([
                    'filter' => new LogicalAnd([
                        new ContentId([4, 12, 13, 42, 59]),
                        new SubdocumentQuery(
                            'test_content_translation_subdocument',
                            new LogicalNot(
                                new LogicalAnd([
                                    new CustomField('visible_b', Operator::EQ, true),
                                    new CustomField('price_i', Operator::EQ, 40),
                                ]),
                            ),
                        ),
                    ]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                ['languages' => ['ger-DE']],
                [4, 59],
            ],
            [
                new Query([
                    'filter' => new LogicalAnd([
                        new ContentId([4, 12, 13, 42, 59]),
                        new SubdocumentQuery(
                            'test_content_translation_subdocument',
                            new LogicalNot(
                                new LogicalAnd([
                                    new CustomField('visible_b', Operator::EQ, false),
                                    new CustomField('price_i', Operator::EQ, 50),
                                ]),
                            ),
                        ),
                    ]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                ['languages' => ['ger-DE']],
                [4, 59],
            ],
            [
                new Query([
                    'filter' => new LogicalAnd([
                        new ContentId([4, 12, 13, 42, 59]),
                        new LogicalNot(
                            new SubdocumentQuery(
                                'test_content_translation_subdocument',
                                new LogicalAnd([
                                    new CustomField('visible_b', Operator::EQ, true),
                                    new CustomField('price_i', Operator::EQ, 40),
                                ]),
                            ),
                        ),
                    ]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                ['languages' => ['ger-DE']],
                [4, 12, 13, 42],
            ],
            [
                new Query([
                    'filter' => new LogicalAnd([
                        new ContentId([4, 12, 13, 42, 59]),
                        new LogicalNot(
                            new SubdocumentQuery(
                                'test_content_translation_subdocument',
                                new LogicalAnd([
                                    new CustomField('visible_b', Operator::EQ, false),
                                    new CustomField('price_i', Operator::EQ, 50),
                                ]),
                            ),
                        ),
                    ]),
                    'sortClauses' => [new ContentIdSortClause()],
                ]),
                ['languages' => ['ger-DE']],
                [12, 13, 42],
            ],
        ];
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testPrepareTestFixtures(): void
    {
        $repository = $this->getRepository();
        $contentService = $repository->getContentService();

        $contentInfo = $contentService->loadContentInfo(4);
        $draft = $contentService->createContentDraft($contentInfo);
        $updateStruct = $contentService->newContentUpdateStruct();
        $updateStruct->setField('name', 'Benutzer', 'ger-DE');
        $contentService->updateContent($draft->versionInfo, $updateStruct);
        $contentService->publishVersion($draft->versionInfo);

        $contentInfo = $contentService->loadContentInfo(59);
        $draft = $contentService->createContentDraft($contentInfo);
        $updateStruct = $contentService->newContentUpdateStruct();
        $updateStruct->setField('name', 'Partner', 'ger-DE');
        $contentService->updateContent($draft->versionInfo, $updateStruct);
        $contentService->publishVersion($draft->versionInfo);

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
    public function testFindContent(Query $query, array $languageFilter, array $expectedIds): void
    {
        $searchService = $this->getSearchService(false);

        $searchResult = $searchService->findContentInfo($query, $languageFilter);

        $this->assertSearchResultContentIds($searchResult, $expectedIds);
    }
}
