<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Ibexa\Backend;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult as IbexaSearchResult;
use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Backend\SearchQuery;
use Netgen\ContentBrowser\Backend\SearchResult;
use Netgen\ContentBrowser\Backend\SearchResultInterface;
use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Ibexa\Item\Ibexa\IbexaInterface;
use Netgen\ContentBrowser\Ibexa\Item\Ibexa\Item;
use Netgen\ContentBrowser\Item\LocationInterface;

use function array_flip;
use function array_map;
use function count;
use function explode;
use function in_array;
use function is_array;
use function is_string;
use function mb_trim;
use function sprintf;
use function usort;

final class IbexaBackend implements BackendInterface
{
    /**
     * @var string[]|null
     */
    private ?array $locationContentTypes = null;

    /**
     * @var string[]|null
     */
    private ?array $allowedContentTypes = null;

    public function __construct(
        private SearchService $searchService,
        private LocationService $locationService,
        private Configuration $config,
    ) {}

    public function getSections(): iterable
    {
        $sectionIds = $this->getSectionIds();
        if (count($sectionIds) === 0) {
            return [];
        }

        $query = new LocationQuery();
        $query->filter = new Criterion\LocationId($sectionIds);

        $result = $this->searchService->findLocations($query);

        $items = $this->buildItems($result);

        $sortMap = array_flip($sectionIds);

        usort(
            $items,
            static function (LocationInterface $item1, LocationInterface $item2) use ($sortMap): int {
                if ($item1->locationId === $item2->locationId) {
                    return 0;
                }

                return $sortMap[$item1->locationId] <=> $sortMap[$item2->locationId];
            },
        );

        return $items;
    }

    public function loadLocation(int|string $id): Item
    {
        $query = new LocationQuery();
        $query->filter = new Criterion\LocationId((int) $id);

        $result = $this->searchService->findLocations($query);

        if (count($result->searchHits) > 0) {
            return $this->buildItem($result->searchHits[0]);
        }

        throw new NotFoundException(
            sprintf(
                'Location with ID "%s" not found.',
                $id,
            ),
        );
    }

    public function loadItem(int|string $value): Item
    {
        $criteria = [];
        if ($this->config->getItemType() === 'ibexa_location') {
            $criteria[] = new Criterion\LocationId((int) $value);
        } elseif ($this->config->getItemType() === 'ibexa_content') {
            $criteria[] = new Criterion\ContentId((int) $value);
            $criteria[] = new Criterion\Location\IsMainLocation(Criterion\Location\IsMainLocation::MAIN);
        }

        $query = new LocationQuery();
        $query->filter = new Criterion\LogicalAnd($criteria);

        $result = $this->searchService->findLocations($query);

        if (count($result->searchHits) > 0) {
            return $this->buildItem($result->searchHits[0]);
        }

        throw new NotFoundException(
            sprintf(
                'Item with value "%s" not found.',
                $value,
            ),
        );
    }

    public function getSubLocations(LocationInterface $location): iterable
    {
        if (!$location instanceof IbexaInterface) {
            return [];
        }

        $this->locationContentTypes ??= $this->getLocationContentTypes();

        $criteria = [
            new Criterion\ParentLocationId((int) $location->locationId),
        ];

        if (count($this->locationContentTypes) > 0) {
            $criteria[] = new Criterion\ContentTypeIdentifier($this->locationContentTypes);
        }

        $query = new LocationQuery();
        $query->filter = new Criterion\LogicalAnd($criteria);
        $query->limit = 9999;
        $query->sortClauses = $location->location->getSortClauses();

        $result = $this->searchService->findLocations($query);

        return $this->buildItems($result);
    }

    public function getSubLocationsCount(LocationInterface $location): int
    {
        $this->locationContentTypes ??= $this->getLocationContentTypes();

        $criteria = [
            new Criterion\ParentLocationId((int) $location->locationId),
        ];

        if (count($this->locationContentTypes) > 0) {
            $criteria[] = new Criterion\ContentTypeIdentifier($this->locationContentTypes);
        }

        $query = new LocationQuery();
        $query->limit = 0;
        $query->filter = new Criterion\LogicalAnd($criteria);

        $result = $this->searchService->findLocations($query);

        return $result->totalCount ?? 0;
    }

    public function getSubItems(LocationInterface $location, int $offset = 0, int $limit = 25): iterable
    {
        if (!$location instanceof IbexaInterface) {
            return [];
        }

        $criteria = [
            new Criterion\ParentLocationId((int) $location->locationId),
        ];

        $query = new LocationQuery();
        $query->offset = $offset;
        $query->limit = $limit;
        $query->filter = new Criterion\LogicalAnd($criteria);
        $query->sortClauses = $location->location->getSortClauses();

        $result = $this->searchService->findLocations($query);

        return $this->buildItems($result);
    }

    public function getSubItemsCount(LocationInterface $location): int
    {
        $criteria = [
            new Criterion\ParentLocationId((int) $location->locationId),
        ];

        $query = new LocationQuery();
        $query->limit = 0;
        $query->filter = new Criterion\LogicalAnd($criteria);

        $result = $this->searchService->findLocations($query);

        return $result->totalCount ?? 0;
    }

    public function searchItems(SearchQuery $searchQuery): SearchResultInterface
    {
        $query = new LocationQuery();

        $searchText = $searchQuery->searchText;
        if (mb_trim($searchText) !== '') {
            $query->query = new Criterion\FullText($searchText);
        }

        $criteria = [
            new Criterion\Location\IsMainLocation(Criterion\Location\IsMainLocation::MAIN),
        ];

        $searchLocation = $searchQuery->location;
        if ($searchLocation instanceof LocationInterface) {
            $location = $this->locationService->loadLocation((int) $searchLocation->locationId);

            $criteria[] = new Criterion\Subtree($location->pathString);
            $criteria[] = new Criterion\LogicalNot(new Criterion\LocationId($location->id));
        }

        $query->filter = new Criterion\LogicalAnd($criteria);

        $query->offset = $searchQuery->offset;
        $query->limit = $searchQuery->limit;

        $result = $this->searchService->findLocations($query);

        return new SearchResult($this->buildItems($result));
    }

    public function searchItemsCount(SearchQuery $searchQuery): int
    {
        $query = new LocationQuery();

        $searchText = $searchQuery->searchText;
        if (mb_trim($searchText) !== '') {
            $query->query = new Criterion\FullText($searchText);
        }

        $criteria = [
            new Criterion\Location\IsMainLocation(Criterion\Location\IsMainLocation::MAIN),
        ];

        $searchLocation = $searchQuery->location;
        if ($searchLocation instanceof LocationInterface) {
            $location = $this->locationService->loadLocation((int) $searchLocation->locationId);

            $criteria[] = new Criterion\Subtree($location->pathString);
            $criteria[] = new Criterion\LogicalNot(new Criterion\LocationId($location->id));
        }

        $query->filter = new Criterion\LogicalAnd($criteria);

        $query->limit = 0;

        $result = $this->searchService->findLocations($query);

        return $result->totalCount ?? 0;
    }

    /**
     * Builds the item from provided search hit.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit<\Ibexa\Contracts\Core\Repository\Values\Content\Location> $searchHit
     */
    private function buildItem(SearchHit $searchHit): Item
    {
        $location = $searchHit->valueObject;

        return new Item(
            $location,
            $this->config->getItemType() === 'ibexa_location' ?
                $location->id :
                $location->contentInfo->id,
            $this->isSelectable($location->getContent()),
        );
    }

    /**
     * Builds the items from search result and its hits.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult<\Ibexa\Contracts\Core\Repository\Values\Content\Location> $searchResult
     *
     * @return \Netgen\ContentBrowser\Ibexa\Item\Ibexa\Item[]
     */
    private function buildItems(IbexaSearchResult $searchResult): array
    {
        return array_map(
            $this->buildItem(...),
            $searchResult->searchHits,
        );
    }

    /**
     * Returns if the provided content is selectable.
     */
    private function isSelectable(Content $content): bool
    {
        if (!$this->config->hasParameter('allowed_content_types')) {
            return true;
        }

        if ($this->allowedContentTypes === null) {
            $this->allowedContentTypes = [];

            $allowedContentTypes = $this->config->getParameter('allowed_content_types');
            if (is_string($allowedContentTypes) && $allowedContentTypes !== '') {
                $this->allowedContentTypes = array_map(mb_trim(...), explode(',', $allowedContentTypes));
            }
        }

        if (count($this->allowedContentTypes) === 0) {
            return true;
        }

        return in_array($content->getContentType()->identifier, $this->allowedContentTypes, true);
    }

    /**
     * @return string[]
     */
    private function getLocationContentTypes(): array
    {
        if ($this->config->hasParameter('location_content_types')) {
            $locationContentTypes = $this->config->getParameter('location_content_types');
            if (is_string($locationContentTypes) && $locationContentTypes !== '') {
                return array_map(mb_trim(...), explode(',', $locationContentTypes));
            }

            if (is_array($locationContentTypes) && count($locationContentTypes) > 0) {
                return $locationContentTypes;
            }
        }

        return [];
    }

    /**
     * @return int[]
     */
    private function getSectionIds(): array
    {
        if ($this->config->hasParameter('sections')) {
            $sections = $this->config->getParameter('sections');
            if (is_string($sections) && $sections !== '') {
                return array_map(intval(...), explode(',', $sections));
            }

            if (is_array($sections) && count($sections) > 0) {
                return $sections;
            }
        }

        return [];
    }
}
