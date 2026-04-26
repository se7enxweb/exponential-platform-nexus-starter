<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Tests\Integration\API;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Tests\Integration\Core\Repository\BaseTestCase;
use Netgen\IbexaSearchExtra\API\Values\Content\Search\LocationQuery;
use Netgen\IbexaSearchExtra\Tests\API\FullTextCriterion;

use function reset;

/**
 * @group extra-fields
 */
class ExtraFieldsTest extends BaseTestCase
{
    public static function providerForTestFind(): array
    {
        return [
            [
                new LocationQuery([
                    'query' => new FullTextCriterion('*comments*'),
                    'filter' => new Criterion\ContentTypeIdentifier('extra_fields_test'),
                    'extraFields' => [],
                ]),
                [],
            ],
            [
                new LocationQuery([
                    'query' => new FullTextCriterion('comments'),
                    'filter' => new Criterion\ContentTypeIdentifier('extra_fields_test'),
                    'extraFields' => ['extra_prefixed_name_s', 'extra_content_type_identifier_s'],
                ]),
                [
                    'extra_prefixed_name_s' => 'prefix No comments article',
                    'extra_content_type_identifier_s' => 'extra_fields_test',
                ],
            ],
            [
                new LocationQuery([
                    'query' => new FullTextCriterion('comments'),
                    'filter' => new Criterion\ContentTypeIdentifier('extra_fields_test'),
                    'extraFields' => ['extra_comment_count_i', 'extra_has_comments_b'],
                ]),
                [
                    'extra_comment_count_i' => 0,
                    'extra_has_comments_b' => false,
                ],
            ],
            [
                new LocationQuery([
                    'query' => new FullTextCriterion('comments'),
                    'filter' => new Criterion\ContentTypeIdentifier('extra_fields_test'),
                    'extraFields' => ['extra_prefixed_name_s', 'extra_comment_count_i', 'extra_has_comments_b'],
                ]),
                [
                    'extra_prefixed_name_s' => 'prefix No comments article',
                    'extra_comment_count_i' => 0,
                    'extra_has_comments_b' => false,
                ],
            ],
            [
                new LocationQuery([
                    'query' => new FullTextCriterion('popular'),
                    'filter' => new Criterion\ContentTypeIdentifier('extra_fields_test'),
                    'extraFields' => ['extra_prefixed_name_s', 'extra_comment_count_i', 'extra_has_comments_b'],
                ]),
                [
                    'extra_prefixed_name_s' => 'prefix Very popular article',
                    'extra_comment_count_i' => 6,
                    'extra_has_comments_b' => true,
                ],
            ],
            [
                new LocationQuery([
                    'query' => new FullTextCriterion('another'),
                    'filter' => new Criterion\ContentTypeIdentifier('extra_fields_test'),
                    'extraFields' => ['extra_prefixed_name_s', 'extra_comment_count_i', 'extra_has_comments_b', 'extra_content_type_identifier_s'],
                ]),
                [
                    'extra_prefixed_name_s' => 'prefix Just another article',
                    'extra_comment_count_i' => 2,
                    'extra_has_comments_b' => true,
                    'extra_content_type_identifier_s' => 'extra_fields_test',
                ],
            ],
        ];
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentTypeFieldDefinitionValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testPrepareTestFixtures(): void
    {
        $repository = $this->getRepository();
        $contentService = $repository->getContentService();
        $contentTypeService = $repository->getContentTypeService();
        $locationService = $repository->getLocationService();

        $contentTypeGroups = $contentTypeService->loadContentTypeGroups();
        $contentTypeCreateStruct = $contentTypeService->newContentTypeCreateStruct('extra_fields_test');
        $contentTypeCreateStruct->mainLanguageCode = 'eng-GB';
        $contentTypeCreateStruct->names = ['eng-GB' => 'Article'];

        $fieldDefinitionCreateStruct = $contentTypeService->newFieldDefinitionCreateStruct('name', 'ibexa_string');
        $fieldDefinitionCreateStruct->position = 0;
        $contentTypeCreateStruct->addFieldDefinition($fieldDefinitionCreateStruct);

        $contentTypeDraft = $contentTypeService->createContentType($contentTypeCreateStruct, [reset($contentTypeGroups)]);
        $contentTypeService->publishContentTypeDraft($contentTypeDraft);
        $contentType = $contentTypeService->loadContentTypeByIdentifier('extra_fields_test');

        $contentTypeCreateStruct = $contentTypeService->newContentTypeCreateStruct('extra_fields_test_comment');
        $contentTypeCreateStruct->mainLanguageCode = 'eng-GB';
        $contentTypeCreateStruct->names = ['eng-GB' => 'Comment'];

        $fieldDefinitionCreateStruct = $contentTypeService->newFieldDefinitionCreateStruct('comment', 'ibexa_string');
        $fieldDefinitionCreateStruct->position = 0;
        $contentTypeCreateStruct->addFieldDefinition($fieldDefinitionCreateStruct);

        $contentTypeDraft = $contentTypeService->createContentType($contentTypeCreateStruct, [reset($contentTypeGroups)]);
        $contentTypeService->publishContentTypeDraft($contentTypeDraft);
        $commentContentType = $contentTypeService->loadContentTypeByIdentifier('extra_fields_test_comment');

        $values = [
            'No comments article' => [],
            'Very popular article' => ['comment 1', 'another comment', 'test comment', 'comment on comment', 'comment 2', 'test'],
            'Just another article' => ['first comment', 'second comment'],
        ];

        $locationCreateStruct = $locationService->newLocationCreateStruct(2);

        foreach ($values as $title => $comments) {
            $contentCreateStruct = $contentService->newContentCreateStruct($contentType, 'eng-GB');
            $contentCreateStruct->setField('name', $title);
            $contentDraft = $contentService->createContent($contentCreateStruct, [$locationCreateStruct]);
            $articleContent = $contentService->publishVersion($contentDraft->versionInfo);

            foreach ($comments as $comment) {
                $commentLocationCreateStruct = $locationService->newLocationCreateStruct($articleContent->contentInfo->mainLocationId);
                $commentContentCreateStruct = $contentService->newContentCreateStruct($commentContentType, 'eng-GB');
                $commentContentCreateStruct->setField('comment', $comment);
                $commentContentDraft = $contentService->createContent($commentContentCreateStruct, [$commentLocationCreateStruct]);
                $contentService->publishVersion($commentContentDraft->versionInfo);
            }
        }

        $this->refreshSearch($repository);

        $this->addToAssertionCount(1);
    }

    /**
     * @dataProvider providerForTestFind
     *
     * @param string[] $expectedExtraFields
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function testFindContent(Query $query, array $expectedExtraFields): void
    {
        $searchService = $this->getSearchService(false);

        /** @var \Netgen\IbexaSearchExtra\API\Values\Content\Search\SearchResult $searchResult */
        $searchResult = $searchService->findContentInfo($query);

        /** @var \Netgen\IbexaSearchExtra\API\Values\Content\Search\SearchHit $searchHit */
        $searchHit = $searchResult->searchHits[0];

        self::assertEquals($expectedExtraFields, $searchHit->extraFields);
    }

    /**
     * @dataProvider providerForTestFind
     *
     * @param string[] $expectedExtraFields
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function testFindLocations(LocationQuery $query, array $expectedExtraFields): void
    {
        $searchService = $this->getSearchService(false);

        /** @var \Netgen\IbexaSearchExtra\API\Values\Content\Search\SearchResult $searchResult */
        $searchResult = $searchService->findLocations($query);

        /** @var \Netgen\IbexaSearchExtra\API\Values\Content\Search\SearchHit $searchHit */
        $searchHit = $searchResult->searchHits[0];

        self::assertEquals($expectedExtraFields, $searchHit->extraFields);
    }

    protected function getSearchService($initialInitializeFromScratch = true): SearchService
    {
        return $this->getRepository($initialInitializeFromScratch)->getSearchService();
    }
}
