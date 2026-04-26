<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Solr\ResultExtractor;

use Ibexa\Contracts\Core\Persistence\Content\ContentInfo;
use Ibexa\Contracts\Core\Persistence\Content\Handler as ContentHandler;
use Ibexa\Contracts\Core\Persistence\Content\Location;
use Ibexa\Contracts\Core\Persistence\Content\Location\Handler as LocationHandler;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Spellcheck;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult as APISearchResult;
use Ibexa\Contracts\Solr\ResultExtractor\AggregationResultExtractor;
use Ibexa\Solr\Gateway\EndpointRegistry;
use Ibexa\Solr\ResultExtractor as BaseResultExtractor;
use Netgen\IbexaSearchExtra\API\Values\Content\Search\SearchResult;
use Netgen\IbexaSearchExtra\API\Values\Content\Search\Suggestion;
use Netgen\IbexaSearchExtra\API\Values\Content\Search\WordSuggestion;
use Netgen\IbexaSearchExtra\Core\Search\Solr\ResultExtractor;
use RuntimeException;
use stdClass;

use function array_key_exists;
use function count;
use function get_object_vars;
use function method_exists;
use function property_exists;

/**
 * The Loading Result Extractor extracts the value object from the Solr search hit data
 * by loading it from the persistence layer.
 */
final class LoadingResultExtractor extends ResultExtractor
{
    protected ContentHandler $contentHandler;
    protected LocationHandler $locationHandler;
    private BaseResultExtractor $nativeResultExtractor;

    public function __construct(
        ContentHandler $contentHandler,
        LocationHandler $locationHandler,
        BaseResultExtractor $nativeResultExtractor,
        AggregationResultExtractor $aggregationResultExtractor,
        EndpointRegistry $endpointRegistry
    ) {
        $this->contentHandler = $contentHandler;
        $this->locationHandler = $locationHandler;
        $this->nativeResultExtractor = $nativeResultExtractor;

        parent::__construct($aggregationResultExtractor, $endpointRegistry);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException If search $hit could not be handled
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function extractHit($hit): ContentInfo|Location
    {
        if ($hit->document_type_id === 'content') {
            return $this->contentHandler->loadContentInfo($hit->content_id_id);
        }

        if ($hit->document_type_id === 'location') {
            return $this->locationHandler->load($hit->location_id_id);
        }

        throw new RuntimeException(
            sprintf("Extracting documents of type '%s' is not handled.", $hit->document_type_id),
        );
    }

    protected function extractSearchResult(
        stdClass $data,
        array $aggregations = [],
        array $languageFilter = [],
        ?Spellcheck $spellcheck = null,
    ): APISearchResult {
        $searchResult = $this->nativeResultExtractor->extract(
            $data,
            $aggregations,
            $languageFilter,
            $spellcheck,
        );
        $searchResult = new SearchResult(get_object_vars($searchResult));
        $this->replaceExtractedValuesByLoadedValues($searchResult);

        if ($this->isSpellCheckAvailable($data)) {
            $searchResult->suggestion = $this->getSpellCheckSuggestion($data);
        }

        return $searchResult;
    }

    private function replaceExtractedValuesByLoadedValues(SearchResult $searchResult): void
    {
        $valueObjectMapById = $this->loadValueObjectMapById($searchResult);

        foreach ($searchResult->searchHits as $index => $searchHit) {
            $id = $this->getValueObjectId($searchHit->valueObject);

            if (array_key_exists($id, $valueObjectMapById)) {
                $searchHit->valueObject = $valueObjectMapById[$id];
            } else {
                unset($searchResult->searchHits[$index]);
                --$searchResult->totalCount;
            }
        }
    }

    /**
     * @return\Ibexa\Contracts\Core\Persistence\Content\ContentInfo[]
     */
    private function loadValueObjectMapById(SearchResult $searchResult): array
    {
        if (!isset($searchResult->searchHits[0])) {
            return [];
        }

        $idList = $this->extractIdList($searchResult);

        if ($searchResult->searchHits[0]->valueObject instanceof ContentInfo) {
            return $this->loadContentInfoMapByIdList($idList);
        }

        return $this->loadLocationMapByIdList($idList);
    }

    /**
     * @return int[]
     */
    private function extractIdList(SearchResult $searchResult): array
    {
        $idList = [];

        foreach ($searchResult->searchHits as $searchHit) {
            $idList[] = $this->getValueObjectId($searchHit->valueObject);
        }

        return $idList;
    }

    private function getValueObjectId($valueObject)
    {
        if ($valueObject instanceof ContentInfo) {
            return $valueObject->id;
        }

        if ($valueObject instanceof Location) {
            return $valueObject->id;
        }

        throw new RuntimeException("Couldn't handle given value object.");
    }

    /**
     * @param int[] $contentIdList
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\ContentInfo[]
     */
    private function loadContentInfoMapByIdList(array $contentIdList): array
    {
        if (method_exists($this->contentHandler, 'loadContentInfoList')) {
            return $this->contentHandler->loadContentInfoList($contentIdList);
        }

        $contentInfoList = [];

        foreach ($contentIdList as $contentId) {
            try {
                $contentInfoList[$contentId] = $this->contentHandler->loadContentInfo($contentId);
            } catch (NotFoundException) {
                // do nothing
            }
        }

        return $contentInfoList;
    }

    /**
     * @param array $locationIdList
     *
     * @return array|\Ibexa\Contracts\Core\Persistence\Content\ContentInfo[]
     */
    private function loadLocationMapByIdList(array $locationIdList): array
    {
        if (method_exists($this->locationHandler, 'loadList')) {
            return $this->locationHandler->loadList($locationIdList);
        }

        $locationList = [];

        foreach ($locationIdList as $locationId) {
            try {
                $locationList[$locationId] = $this->locationHandler->load($locationId);
            } catch (NotFoundException) {
                // do nothing
            }
        }

        return $locationList;
    }

    /**
     * @param $data
     *
     * @return bool
     */
    private function isSpellCheckAvailable($data): bool
    {
        return property_exists($data, 'spellcheck') && property_exists($data->spellcheck, 'suggestions');
    }

    /**
     * Extracts spell check suggestions from received data.
     *
     * @param $data
     *
     * @return \Netgen\IbexaSearchExtra\API\Values\Content\Search\Suggestion
     */
    private function getSpellCheckSuggestion($data): Suggestion
    {
        $receivedSuggestions = (array) $data->spellcheck->suggestions;
        $wordSuggestions = [];

        for ($i = 0; $i < (count($receivedSuggestions) - 1); $i += 2) {
            $originalWord = $receivedSuggestions[$i];
            $receivedWordSuggestions = $receivedSuggestions[$i + 1];

            if (!property_exists($receivedWordSuggestions, 'suggestion') || empty($receivedWordSuggestions->suggestion)) {
                continue;
            }

            foreach ($receivedWordSuggestions->suggestion as $suggestion) {
                $wordSuggestions[] = new WordSuggestion([
                    'originalWord' => (string) $originalWord,
                    'suggestedWord' => (string) $suggestion->word,
                    'frequency' => (int) $suggestion->freq,
                ]);
            }
        }

        return new Suggestion($wordSuggestions);
    }
}
