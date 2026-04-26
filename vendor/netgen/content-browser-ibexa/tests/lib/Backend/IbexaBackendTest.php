<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Ibexa\Tests\Backend;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location as ContractsLocation;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\ContentName;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\Location;
use Netgen\ContentBrowser\Backend\SearchQuery;
use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Ibexa\Backend\IbexaBackend;
use Netgen\ContentBrowser\Ibexa\Item\Ibexa\Item;
use Netgen\ContentBrowser\Ibexa\Tests\Stubs\Location as StubLocation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(IbexaBackend::class)]
final class IbexaBackendTest extends TestCase
{
    private Stub&SearchService $searchServiceStub;

    private Stub&LocationService $locationServiceStub;

    /**
     * @var string[]
     */
    private array $locationContentTypes;

    /**
     * @var int[]
     */
    private array $defaultSections;

    private IbexaBackend $backend;

    protected function setUp(): void
    {
        $this->defaultSections = [2, 43, 5];
        $this->locationContentTypes = ['frontpage', 'category'];

        $this->searchServiceStub = self::createStub(SearchService::class);
        $this->locationServiceStub = self::createStub(LocationService::class);

        $configuration = new Configuration('ibexa_location', 'Ibexa location', []);
        $configuration->setParameter('sections', $this->defaultSections);
        $configuration->setParameter('location_content_types', $this->locationContentTypes);

        $this->backend = new IbexaBackend(
            $this->searchServiceStub,
            $this->locationServiceStub,
            $configuration,
        );
    }

    public function testGetSections(): void
    {
        $query = new LocationQuery();
        $query->filter = new Criterion\LocationId($this->defaultSections);

        $searchResult = new SearchResult();
        $searchResult->searchHits = [
            new SearchHit(['valueObject' => $this->getLocation(2)]),
            new SearchHit(['valueObject' => $this->getLocation(43)]),
            new SearchHit(['valueObject' => $this->getLocation(5)]),
        ];

        $this->searchServiceStub
            ->method('findLocations')
            ->willReturn($searchResult);

        $locations = $this->backend->getSections();

        self::assertCount(3, $locations);
        self::assertContainsOnlyInstancesOf(Item::class, $locations);
    }

    public function testLoadLocation(): void
    {
        $query = new LocationQuery();
        $query->filter = new Criterion\LocationId(2);

        $searchResult = new SearchResult();
        $searchResult->searchHits = [
            new SearchHit(['valueObject' => $this->getLocation(2)]),
        ];

        $this->searchServiceStub
            ->method('findLocations')
            ->willReturn($searchResult);

        $location = $this->backend->loadLocation(2);

        self::assertSame(2, $location->locationId);
    }

    public function testLoadLocationThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Location with ID "2" not found.');

        $query = new LocationQuery();
        $query->filter = new Criterion\LocationId(2);

        $searchResult = new SearchResult();
        $searchResult->searchHits = [];

        $this->searchServiceStub
            ->method('findLocations')
            ->willReturn($searchResult);

        $this->backend->loadLocation(2);
    }

    public function testLoadItem(): void
    {
        $query = new LocationQuery();
        $query->filter = new Criterion\LogicalAnd(
            [
                new Criterion\LocationId(2),
            ],
        );

        $searchResult = new SearchResult();
        $searchResult->searchHits = [
            new SearchHit(['valueObject' => $this->getLocation(2)]),
        ];

        $this->searchServiceStub
            ->method('findLocations')
            ->willReturn($searchResult);

        $item = $this->backend->loadItem(2);

        self::assertSame(2, $item->value);
    }

    public function testLoadItemWithContent(): void
    {
        $this->backend = new IbexaBackend(
            $this->searchServiceStub,
            $this->locationServiceStub,
            new Configuration('ibexa_content', 'Ibexa content', []),
        );

        $query = new LocationQuery();
        $query->filter = new Criterion\LogicalAnd(
            [
                new Criterion\ContentId(2),
                new Criterion\Location\IsMainLocation(Criterion\Location\IsMainLocation::MAIN),
            ],
        );

        $searchResult = new SearchResult();
        $searchResult->searchHits = [
            new SearchHit(['valueObject' => $this->getLocation(0, 0, 2)]),
        ];

        $this->searchServiceStub
            ->method('findLocations')
            ->willReturn($searchResult);

        $item = $this->backend->loadItem(2);

        self::assertSame(2, $item->value);
    }

    public function testLoadItemThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Item with value "2" not found.');

        $query = new LocationQuery();
        $query->filter = new Criterion\LogicalAnd(
            [
                new Criterion\LocationId(2),
            ],
        );

        $searchResult = new SearchResult();
        $searchResult->searchHits = [];

        $this->searchServiceStub
            ->method('findLocations')
            ->willReturn($searchResult);

        $this->backend->loadItem(2);
    }

    public function testGetSubLocations(): void
    {
        $query = new LocationQuery();
        $query->offset = 0;
        $query->limit = 9999;
        $query->filter = new Criterion\LogicalAnd(
            [
                new Criterion\ParentLocationId(2),
                new Criterion\ContentTypeIdentifier($this->locationContentTypes),
            ],
        );

        $query->sortClauses = [new ContentName(Query::SORT_ASC)];

        $searchResult = new SearchResult();
        $searchResult->searchHits = [
            new SearchHit(['valueObject' => $this->getLocation(0, 2)]),
            new SearchHit(['valueObject' => $this->getLocation(0, 2)]),
        ];

        $this->searchServiceStub
            ->method('findLocations')
            ->willReturn($searchResult);

        $locations = $this->backend->getSubLocations(
            new Item($this->getLocation(2), 2),
        );

        self::assertCount(2, $locations);
        self::assertContainsOnlyInstancesOf(Item::class, $locations);

        foreach ($locations as $location) {
            self::assertSame(2, $location->parentId);
        }
    }

    public function testGetSubLocationsWithInvalidItem(): void
    {
        $locations = $this->backend->getSubLocations(new StubLocation(0));

        self::assertIsArray($locations);
        self::assertEmpty($locations);
    }

    public function testGetSubLocationsCount(): void
    {
        $query = new LocationQuery();
        $query->limit = 0;
        $query->filter = new Criterion\LogicalAnd(
            [
                new Criterion\ParentLocationId(2),
                new Criterion\ContentTypeIdentifier($this->locationContentTypes),
            ],
        );

        $searchResult = new SearchResult();
        $searchResult->totalCount = 2;

        $this->searchServiceStub
            ->method('findLocations')
            ->willReturn($searchResult);

        $count = $this->backend->getSubLocationsCount(
            new Item($this->getLocation(2), 2),
        );

        self::assertSame(2, $count);
    }

    public function testGetSubItems(): void
    {
        $query = new LocationQuery();
        $query->offset = 0;
        $query->limit = 25;
        $query->filter = new Criterion\LogicalAnd(
            [
                new Criterion\ParentLocationId(2),
            ],
        );

        $query->sortClauses = [new ContentName(Query::SORT_ASC)];

        $searchResult = new SearchResult();
        $searchResult->searchHits = [
            new SearchHit(['valueObject' => $this->getLocation(0, 2)]),
            new SearchHit(['valueObject' => $this->getLocation(0, 2)]),
        ];

        $this->searchServiceStub
            ->method('findLocations')
            ->willReturn($searchResult);

        $items = $this->backend->getSubItems(
            new Item($this->getLocation(2), 2),
        );

        self::assertCount(2, $items);
        self::assertContainsOnlyInstancesOf(Item::class, $items);

        foreach ($items as $item) {
            // Additional InstanceOf assertion to make PHPStan happy
            self::assertInstanceOf(Item::class, $item);
            self::assertSame(2, $item->parentId);
        }
    }

    public function testGetSubItemsWithOffsetAndLimit(): void
    {
        $query = new LocationQuery();
        $query->offset = 5;
        $query->limit = 10;
        $query->filter = new Criterion\LogicalAnd(
            [
                new Criterion\ParentLocationId(2),
            ],
        );

        $query->sortClauses = [new ContentName(Query::SORT_ASC)];

        $searchResult = new SearchResult();
        $searchResult->searchHits = [
            new SearchHit(['valueObject' => $this->getLocation(0, 2)]),
            new SearchHit(['valueObject' => $this->getLocation(0, 2)]),
        ];

        $this->searchServiceStub
            ->method('findLocations')
            ->willReturn($searchResult);

        $items = $this->backend->getSubItems(
            new Item($this->getLocation(2), 2),
            5,
            10,
        );

        self::assertCount(2, $items);
        self::assertContainsOnlyInstancesOf(Item::class, $items);

        foreach ($items as $item) {
            // Additional InstanceOf assertion to make PHPStan happy
            self::assertInstanceOf(Item::class, $item);
            self::assertSame(2, $item->parentId);
        }
    }

    public function testGetSubItemsWithInvalidItem(): void
    {
        $items = $this->backend->getSubItems(new StubLocation(0));

        self::assertIsArray($items);
        self::assertEmpty($items);
    }

    public function testGetSubItemsCount(): void
    {
        $query = new LocationQuery();
        $query->limit = 0;
        $query->filter = new Criterion\LogicalAnd(
            [
                new Criterion\ParentLocationId(2),
            ],
        );

        $searchResult = new SearchResult();
        $searchResult->totalCount = 2;

        $this->searchServiceStub
            ->method('findLocations')
            ->willReturn($searchResult);

        $count = $this->backend->getSubItemsCount(
            new Item($this->getLocation(2), 2),
        );

        self::assertSame(2, $count);
    }

    public function testSearchItems(): void
    {
        $searchQuery = new LocationQuery();
        $searchQuery->offset = 0;
        $searchQuery->limit = 25;
        $searchQuery->query = new Criterion\FullText('test');
        $searchQuery->filter = new Criterion\LogicalAnd(
            [
                new Criterion\Location\IsMainLocation(Criterion\Location\IsMainLocation::MAIN),
            ],
        );

        $searchResult = new SearchResult();
        $searchResult->searchHits = [
            new SearchHit(['valueObject' => $this->getLocation()]),
            new SearchHit(['valueObject' => $this->getLocation()]),
        ];

        $this->searchServiceStub
            ->method('findLocations')
            ->willReturn($searchResult);

        $result = $this->backend->searchItems(new SearchQuery('test'));

        self::assertCount(2, $result->results);
        self::assertContainsOnlyInstancesOf(Item::class, $result->results);
    }

    public function testSearchItemsWithOffsetAndLimit(): void
    {
        $searchQuery = new LocationQuery();
        $searchQuery->offset = 5;
        $searchQuery->limit = 10;
        $searchQuery->query = new Criterion\FullText('test');
        $searchQuery->filter = new Criterion\LogicalAnd(
            [
                new Criterion\Location\IsMainLocation(Criterion\Location\IsMainLocation::MAIN),
            ],
        );

        $searchResult = new SearchResult();
        $searchResult->searchHits = [
            new SearchHit(['valueObject' => $this->getLocation()]),
            new SearchHit(['valueObject' => $this->getLocation()]),
        ];

        $this->searchServiceStub
            ->method('findLocations')
            ->willReturn($searchResult);

        $query = new SearchQuery('test');
        $query->offset = 5;
        $query->limit = 10;

        $result = $this->backend->searchItems($query);

        self::assertCount(2, $result->results);
        self::assertContainsOnlyInstancesOf(Item::class, $result->results);
    }

    public function testSearchItemsCount(): void
    {
        $searchQuery = new LocationQuery();
        $searchQuery->limit = 0;
        $searchQuery->query = new Criterion\FullText('test');
        $searchQuery->filter = new Criterion\LogicalAnd(
            [
                new Criterion\Location\IsMainLocation(Criterion\Location\IsMainLocation::MAIN),
            ],
        );

        $searchResult = new SearchResult();
        $searchResult->totalCount = 2;

        $this->searchServiceStub
            ->method('findLocations')
            ->willReturn($searchResult);

        $count = $this->backend->searchItemsCount(new SearchQuery('test'));

        self::assertSame(2, $count);
    }

    /**
     * Returns the location object used in tests.
     */
    private function getLocation(int $id = 0, int $parentLocationId = 0, int $contentId = 0): Location
    {
        return new Location(
            [
                'id' => $id,
                'parentLocationId' => $parentLocationId,
                'content' => new Content(),
                'contentInfo' => new ContentInfo(
                    [
                        'id' => $contentId,
                    ],
                ),
                'sortField' => ContractsLocation::SORT_FIELD_NAME,
                'sortOrder' => ContractsLocation::SORT_ORDER_ASC,
            ],
        );
    }
}
