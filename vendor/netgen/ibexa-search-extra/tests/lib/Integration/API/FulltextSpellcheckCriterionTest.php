<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Tests\Integration\API;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Tests\Integration\Core\Repository\BaseTestCase;
use Netgen\IbexaSearchExtra\API\Values\Content\Search\WordSuggestion;
use Netgen\IbexaSearchExtra\Tests\API\FullTextCriterion;

use function getenv;
use function in_array;
use function reset;

/**
 * @group fulltext-spellcheck
 */
class FulltextSpellcheckCriterionTest extends BaseTestCase
{
    public static function providerForTestFind(): array
    {
        return [
            [
                new LocationQuery([
                    'query' => new FullTextCriterion('sucess'),
                    'filter' => new Criterion\ContentTypeIdentifier('spellcheck_test'),
                ]),
                [
                    [
                        new WordSuggestion([
                            'originalWord' => 'sucess',
                            'suggestedWord' => 'success',
                            'frequency' => 3,
                        ]),
                    ],
                ],
            ],
            [
                new LocationQuery([
                    'query' => new FullTextCriterion('sucessful'),
                    'filter' => new Criterion\ContentTypeIdentifier('spellcheck_test'),
                ]),
                [
                    [
                        new WordSuggestion([
                            'originalWord' => 'sucessful',
                            'suggestedWord' => 'successful',
                            'frequency' => 1,
                        ]),
                        new WordSuggestion([
                            'originalWord' => 'sucessful',
                            'suggestedWord' => 'successfully',
                            'frequency' => 2,
                        ]),
                    ],
                ],
            ],
            [
                new LocationQuery([
                    'query' => new FullTextCriterion('success'),
                    'filter' => new Criterion\ContentTypeIdentifier('spellcheck_test'),
                ]),
                [
                    [
                        new WordSuggestion([
                            'originalWord' => 'success',
                            'suggestedWord' => 'successful',
                            'frequency' => 1,
                        ]),
                    ],
                ],
            ],
            [
                new LocationQuery([
                    'query' => new FullTextCriterion('medioccre'),
                    'filter' => new Criterion\ContentTypeIdentifier('spellcheck_test'),
                ]),
                [
                    [
                        new WordSuggestion([
                            'originalWord' => 'medioccre',
                            'suggestedWord' => 'mediocre',
                            'frequency' => 1,
                        ]),
                    ],
                ],
            ],
            [
                new LocationQuery([
                    'query' => new FullTextCriterion('mediocre'),
                    'filter' => new Criterion\ContentTypeIdentifier('spellcheck_test'),
                ]),
                [],
            ],
            [
                new LocationQuery([
                    'query' => new FullTextCriterion('asdfghjk'),
                    'filter' => new Criterion\ContentTypeIdentifier('spellcheck_test'),
                ]),
                [],
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
        $contentTypeCreateStruct = $contentTypeService->newContentTypeCreateStruct('spellcheck_test');
        $contentTypeCreateStruct->mainLanguageCode = 'eng-GB';
        $contentTypeCreateStruct->names = ['eng-GB' => 'Article'];
        $fieldDefinitionCreateStruct = $contentTypeService->newFieldDefinitionCreateStruct('name', 'ibexa_string');
        $fieldDefinitionCreateStruct->position = 0;
        $contentTypeCreateStruct->addFieldDefinition($fieldDefinitionCreateStruct);
        $fieldDefinitionCreateStruct = $contentTypeService->newFieldDefinitionCreateStruct('description', 'ibexa_string');
        $fieldDefinitionCreateStruct->position = 1;
        $contentTypeCreateStruct->addFieldDefinition($fieldDefinitionCreateStruct);
        $contentTypeDraft = $contentTypeService->createContentType($contentTypeCreateStruct, [reset($contentTypeGroups)]);
        $contentTypeService->publishContentTypeDraft($contentTypeDraft);
        $contentType = $contentTypeService->loadContentTypeByIdentifier('spellcheck_test');

        $values = [
            'Test content 1' => 'This content has been published successfully',
            'Test content 2' => 'This is a success',
            'Test content 3' => 'This is a successful content',
            'Test content 4' => 'This content needs a success',
            'Test content 5' => 'Testing if success word is spelled successfully',
            'Test content 6' => 'This content is mediocre',
        ];

        $locationCreateStruct = $locationService->newLocationCreateStruct(2);

        foreach ($values as $title => $description) {
            $contentCreateStruct = $contentService->newContentCreateStruct($contentType, 'eng-GB');
            $contentCreateStruct->setField('name', $title);
            $contentCreateStruct->setField('description', $description);
            $contentDraft = $contentService->createContent($contentCreateStruct, [$locationCreateStruct]);
            $contentService->publishVersion($contentDraft->versionInfo);
        }

        $this->refreshSearch($repository);

        self::assertTrue(true);
    }

    /**
     * @dataProvider providerForTestFind
     *
     * @param \Netgen\IbexaSearchExtra\API\Values\Content\Search\WordSuggestion[] $expectedWordSuggestions
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function testFindContent(Query $query, array $expectedWordSuggestions): void
    {
        $searchService = $this->getSearchService(false);

        /** @var \Netgen\IbexaSearchExtra\API\Values\Content\Search\SearchResult $searchResult */
        $searchResult = $searchService->findContentInfo($query);

        $this->correctFrequencyForCloudSetup($expectedWordSuggestions);

        self::assertEquals($expectedWordSuggestions, $searchResult->suggestion->getSuggestions());
    }

    /**
     * @dataProvider providerForTestFind
     *
     * @param \Netgen\IbexaSearchExtra\API\Values\Content\Search\WordSuggestion[] $expectedWordSuggestions
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function testFindLocations(LocationQuery $query, array $expectedWordSuggestions): void
    {
        $searchService = $this->getSearchService(false);

        /** @var \Netgen\IbexaSearchExtra\API\Values\Content\Search\SearchResult $searchResult */
        $searchResult = $searchService->findLocations($query);

        $this->correctFrequencyForCloudSetup($expectedWordSuggestions);

        self::assertEquals($expectedWordSuggestions, $searchResult->suggestion->getSuggestions());
    }

    protected function getSearchService($initialInitializeFromScratch = true): SearchService
    {
        return $this->getRepository($initialInitializeFromScratch)->getSearchService();
    }

    /**
     * For some reason frequency is doubled in the cloud and shared setups.
     * TODO: investigate why.
     *
     * @param \Netgen\IbexaSearchExtra\API\Values\Content\Search\WordSuggestion[] $wordSuggestions
     */
    protected function correctFrequencyForCloudSetup(array $wordSuggestions): void
    {
        $coreSetup = getenv('CORES_SETUP');
        $correctedSetups = ['cloud', 'shared'];

        if (in_array($coreSetup, $correctedSetups, true)) {
            foreach ($wordSuggestions as $wordSuggestionsGroup) {
                foreach ($wordSuggestionsGroup as $wordSuggestion) {
                    $wordSuggestion->frequency *= 2;
                }
            }
        }
    }
}
