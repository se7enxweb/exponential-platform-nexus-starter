<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Tests\Integration\API;

use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\ContentId;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\Location\Id as LocationId;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\Visible;
use function reset;

class VisibleCriterionTest extends BaseTestCase
{
    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testFindVisibleContent(): void
    {
        $repository = $this->getRepository();
        $searchService = $repository->getSearchService();
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentA */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentB */
        [$contentA, $contentB] = $this->prepareTestFixtures();
        $this->refreshSearch($repository);

        $searchResultVisible = $searchService->findContent($this->getContentQuery(true));

        self::assertSame(2, $searchResultVisible->totalCount);
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content1 */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content2 */
        $content1 = $searchResultVisible->searchHits[0]->valueObject;
        $content2 = $searchResultVisible->searchHits[1]->valueObject;
        self::assertSame($contentA->id, $content1->id);
        self::assertSame($contentB->id, $content2->id);

        $searchResultNotVisible = $searchService->findContent($this->getContentQuery(false));

        self::assertSame(0, $searchResultNotVisible->totalCount);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testFindHiddenContent(): void
    {
        $repository = $this->getRepository();
        $searchService = $repository->getSearchService();
        $contentService = $repository->getContentService();
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentA */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentB */
        [$contentA, $contentB] = $this->prepareTestFixtures();

        $contentService->hideContent($contentA->contentInfo);
        $this->refreshSearch($repository);

        $searchResultNotVisible = $searchService->findContent($this->getContentQuery(false));

        self::assertSame(1, $searchResultNotVisible->totalCount);

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content1 */
        $content1 = $searchResultNotVisible->searchHits[0]->valueObject;
        self::assertSame($contentA->id, $content1->id);

        $searchResultVisible = $searchService->findContent($this->getContentQuery(true));

        self::assertSame(1, $searchResultVisible->totalCount);

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content2 */
        $content2 = $searchResultVisible->searchHits[0]->valueObject;
        self::assertSame($contentB->id, $content2->id);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testFindVisibleLocation(): void
    {
        $repository = $this->getRepository();
        $searchService = $repository->getSearchService();
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentA */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentB */
        [$contentA, $contentB] = $this->prepareTestFixtures();
        $this->refreshSearch($repository);

        $searchResultVisible = $searchService->findLocations($this->getLocationQuery(true));

        self::assertSame(2, $searchResultVisible->totalCount);
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location1 */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location2 */
        $location1 = $searchResultVisible->searchHits[0]->valueObject;
        $location2 = $searchResultVisible->searchHits[1]->valueObject;
        self::assertSame($contentA->contentInfo->mainLocationId, $location1->id);
        self::assertSame($contentB->contentInfo->mainLocationId, $location2->id);

        $searchResultNotVisible = $searchService->findLocations($this->getLocationQuery(false));

        self::assertSame(0, $searchResultNotVisible->totalCount);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testFindHiddenLocation(): void
    {
        $repository = $this->getRepository();
        $searchService = $repository->getSearchService();
        $locationService = $repository->getLocationService();
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentA */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentB */
        [$contentA, $contentB] = $this->prepareTestFixtures();

        $locationB = $locationService->loadLocation($contentB->contentInfo->mainLocationId);
        $locationService->hideLocation($locationB);
        $this->refreshSearch($repository);

        $searchResultNotVisible = $searchService->findLocations($this->getLocationQuery(false));

        self::assertSame(1, $searchResultNotVisible->totalCount);

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location2 */
        $location2 = $searchResultNotVisible->searchHits[0]->valueObject;
        self::assertSame($contentB->contentInfo->mainLocationId, $location2->id);

        $searchResultVisible = $searchService->findLocations($this->getLocationQuery(true));

        self::assertSame(1, $searchResultVisible->totalCount);

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location1 */
        $location1 = $searchResultVisible->searchHits[0]->valueObject;
        self::assertSame($contentA->contentInfo->mainLocationId, $location1->id);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testFindInvisibleLocation(): void
    {
        $repository = $this->getRepository();
        $searchService = $repository->getSearchService();
        $locationService = $repository->getLocationService();
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentA */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentB */
        [$contentA, $contentB] = $this->prepareTestFixtures();

        $locationA = $locationService->loadLocation($contentA->contentInfo->mainLocationId);
        $locationService->hideLocation($locationA);
        $this->refreshSearch($repository);

        $searchResultNotVisible = $searchService->findLocations($this->getLocationQuery(false));

        self::assertSame(2, $searchResultNotVisible->totalCount);
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location1 */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location2 */
        $location1 = $searchResultNotVisible->searchHits[0]->valueObject;
        $location2 = $searchResultNotVisible->searchHits[1]->valueObject;
        self::assertSame($contentA->contentInfo->mainLocationId, $location1->id);
        self::assertSame($contentB->contentInfo->mainLocationId, $location2->id);

        $searchResultVisible = $searchService->findLocations($this->getLocationQuery(true));

        self::assertSame(0, $searchResultVisible->totalCount);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testFindLocationHiddenContent(): void
    {
        $repository = $this->getRepository();
        $searchService = $repository->getSearchService();
        $contentService = $repository->getContentService();
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentA */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentB */
        [$contentA, $contentB] = $this->prepareTestFixtures();

        $contentService->hideContent($contentB->contentInfo);
        $this->refreshSearch($repository);

        $searchResultNotVisible = $searchService->findLocations($this->getLocationQuery(false));

        self::assertSame(1, $searchResultNotVisible->totalCount);

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location2 */
        $location2 = $searchResultNotVisible->searchHits[0]->valueObject;
        self::assertSame($contentB->contentInfo->mainLocationId, $location2->id);

        $searchResultVisible = $searchService->findLocations($this->getLocationQuery(true));

        self::assertSame(1, $searchResultVisible->totalCount);

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location1 */
        $location1 = $searchResultVisible->searchHits[0]->valueObject;
        self::assertSame($contentA->contentInfo->mainLocationId, $location1->id);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testFindLocationHiddenParentContent(): void
    {
        $repository = $this->getRepository();
        $searchService = $repository->getSearchService();
        $contentService = $repository->getContentService();
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentA */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentB */
        [$contentA, $contentB] = $this->prepareTestFixtures();

        $contentService->hideContent($contentA->contentInfo);
        $this->refreshSearch($repository);

        $searchResultNotVisible = $searchService->findLocations($this->getLocationQuery(false));

        self::assertSame(2, $searchResultNotVisible->totalCount);
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location1 */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location2 */
        $location1 = $searchResultNotVisible->searchHits[0]->valueObject;
        $location2 = $searchResultNotVisible->searchHits[1]->valueObject;
        self::assertSame($contentA->contentInfo->mainLocationId, $location1->id);
        self::assertSame($contentB->contentInfo->mainLocationId, $location2->id);

        $searchResultVisible = $searchService->findLocations($this->getLocationQuery(true));

        self::assertSame(0, $searchResultVisible->totalCount);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testFindLocationHiddenParentContentReveal(): void
    {
        $repository = $this->getRepository();
        $searchService = $repository->getSearchService();
        $contentService = $repository->getContentService();
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentA */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentB */
        [$contentA, $contentB] = $this->prepareTestFixtures();

        $contentService->hideContent($contentA->contentInfo);
        $contentService->revealContent($contentA->contentInfo);
        $this->refreshSearch($repository);

        $searchResultNotVisible = $searchService->findLocations($this->getLocationQuery(false));

        self::assertSame(0, $searchResultNotVisible->totalCount);

        $searchResultVisible = $searchService->findLocations($this->getLocationQuery(true));

        self::assertSame(2, $searchResultVisible->totalCount);
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location1 */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location2 */
        $location1 = $searchResultVisible->searchHits[0]->valueObject;
        $location2 = $searchResultVisible->searchHits[1]->valueObject;
        self::assertSame($contentA->contentInfo->mainLocationId, $location1->id);
        self::assertSame($contentB->contentInfo->mainLocationId, $location2->id);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testFindInvisibleParentLocationHiddenParentContentReveal(): void
    {
        $repository = $this->getRepository();
        $searchService = $repository->getSearchService();
        $contentService = $repository->getContentService();
        $locationService = $repository->getLocationService();
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentA */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentB */
        [$contentA, $contentB] = $this->prepareTestFixtures();

        $locationA = $locationService->loadLocation($contentA->contentInfo->mainLocationId);
        $locationService->hideLocation($locationA);
        $contentService->hideContent($contentA->contentInfo);
        $contentService->revealContent($contentA->contentInfo);
        $this->refreshSearch($repository);

        $searchResultNotVisible = $searchService->findLocations($this->getLocationQuery(false));

        self::assertSame(2, $searchResultNotVisible->totalCount);
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location1 */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location2 */
        $location1 = $searchResultNotVisible->searchHits[0]->valueObject;
        $location2 = $searchResultNotVisible->searchHits[1]->valueObject;
        self::assertSame($contentA->contentInfo->mainLocationId, $location1->id);
        self::assertSame($contentB->contentInfo->mainLocationId, $location2->id);

        $searchResultVisible = $searchService->findLocations($this->getLocationQuery(true));

        self::assertSame(0, $searchResultVisible->totalCount);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testFindInvisibleChildLocationHiddenParentContentReveal(): void
    {
        $repository = $this->getRepository();
        $searchService = $repository->getSearchService();
        $contentService = $repository->getContentService();
        $locationService = $repository->getLocationService();
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentA */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentB */
        [$contentA, $contentB] = $this->prepareTestFixtures();

        $locationB = $locationService->loadLocation($contentB->contentInfo->mainLocationId);
        $locationService->hideLocation($locationB);
        $contentService->hideContent($contentA->contentInfo);
        $contentService->revealContent($contentA->contentInfo);
        $this->refreshSearch($repository);

        $searchResultNotVisible = $searchService->findLocations($this->getLocationQuery(false));

        self::assertSame(1, $searchResultNotVisible->totalCount);

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location2 */
        $location2 = $searchResultNotVisible->searchHits[0]->valueObject;
        self::assertSame($contentB->contentInfo->mainLocationId, $location2->id);

        $searchResultVisible = $searchService->findLocations($this->getLocationQuery(true));

        self::assertSame(1, $searchResultVisible->totalCount);

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location1 */
        $location1 = $searchResultVisible->searchHits[0]->valueObject;
        self::assertSame($contentA->contentInfo->mainLocationId, $location1->id);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testFindVisibleChildAdditionalLocationHiddenParentContent(): void
    {
        $repository = $this->getRepository();
        $searchService = $repository->getSearchService();
        $contentService = $repository->getContentService();
        $locationService = $repository->getLocationService();
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentA */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentB */
        [$contentA, $contentB] = $this->prepareTestFixtures();

        $locationCreateStruct = $locationService->newLocationCreateStruct(2);
        $additionalLocation = $locationService->createLocation($contentB->contentInfo, $locationCreateStruct);
        $contentService->hideContent($contentA->contentInfo);
        $this->refreshSearch($repository);

        $searchResultNotVisible = $searchService->findLocations($this->getLocationQuery(false));

        self::assertSame(2, $searchResultNotVisible->totalCount);
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location1 */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location2 */
        $location1 = $searchResultNotVisible->searchHits[0]->valueObject;
        $location2 = $searchResultNotVisible->searchHits[1]->valueObject;
        self::assertSame($contentA->contentInfo->mainLocationId, $location1->id);
        self::assertSame($contentB->contentInfo->mainLocationId, $location2->id);

        $searchResultVisible = $searchService->findLocations($this->getLocationQuery(true));

        self::assertSame(1, $searchResultVisible->totalCount);

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location3 */
        $location3 = $searchResultVisible->searchHits[0]->valueObject;
        self::assertSame($additionalLocation->id, $location3->id);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testFindVisibleChildAdditionalLocationHiddenParentContentVariant(): void
    {
        $repository = $this->getRepository();
        $searchService = $repository->getSearchService();
        $contentService = $repository->getContentService();
        $locationService = $repository->getLocationService();
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentA */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentB */
        [$contentA, $contentB] = $this->prepareTestFixtures();

        $contentService->hideContent($contentA->contentInfo);
        $locationCreateStruct = $locationService->newLocationCreateStruct(2);
        $additionalLocation = $locationService->createLocation($contentB->contentInfo, $locationCreateStruct);
        $this->refreshSearch($repository);

        $searchResultNotVisible = $searchService->findLocations($this->getLocationQuery(false));

        self::assertSame(2, $searchResultNotVisible->totalCount);
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location1 */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location2 */
        $location1 = $searchResultNotVisible->searchHits[0]->valueObject;
        $location2 = $searchResultNotVisible->searchHits[1]->valueObject;
        self::assertSame($contentA->contentInfo->mainLocationId, $location1->id);
        self::assertSame($contentB->contentInfo->mainLocationId, $location2->id);

        $searchResultVisible = $searchService->findLocations($this->getLocationQuery(true));

        self::assertSame(1, $searchResultVisible->totalCount);

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location3 */
        $location3 = $searchResultVisible->searchHits[0]->valueObject;
        self::assertSame($additionalLocation->id, $location3->id);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testFindInvisibleChildAdditionalLocationHiddenChildContent(): void
    {
        $repository = $this->getRepository();
        $searchService = $repository->getSearchService();
        $contentService = $repository->getContentService();
        $locationService = $repository->getLocationService();
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentA */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentB */
        [$contentA, $contentB] = $this->prepareTestFixtures();

        $locationCreateStruct = $locationService->newLocationCreateStruct(2);
        $additionalLocation = $locationService->createLocation($contentB->contentInfo, $locationCreateStruct);
        $contentService->hideContent($contentB->contentInfo);
        $this->refreshSearch($repository);

        $searchResultNotVisible = $searchService->findLocations($this->getLocationQuery(false));

        self::assertSame(2, $searchResultNotVisible->totalCount);
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location2 */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location3 */
        $location2 = $searchResultNotVisible->searchHits[0]->valueObject;
        $location3 = $searchResultNotVisible->searchHits[1]->valueObject;
        self::assertSame($contentB->contentInfo->mainLocationId, $location2->id);
        self::assertSame($additionalLocation->id, $location3->id);

        $searchResultVisible = $searchService->findLocations($this->getLocationQuery(true));

        self::assertSame(1, $searchResultVisible->totalCount);

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location1 */
        $location1 = $searchResultVisible->searchHits[0]->valueObject;
        self::assertSame($contentA->contentInfo->mainLocationId, $location1->id);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testFindInvisibleChildAdditionalLocationHiddenChildContentVariant(): void
    {
        $repository = $this->getRepository();
        $searchService = $repository->getSearchService();
        $contentService = $repository->getContentService();
        $locationService = $repository->getLocationService();
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentA */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentB */
        [$contentA, $contentB] = $this->prepareTestFixtures();

        $contentService->hideContent($contentB->contentInfo);
        $locationCreateStruct = $locationService->newLocationCreateStruct(2);
        $additionalLocation = $locationService->createLocation($contentB->contentInfo, $locationCreateStruct);
        $this->refreshSearch($repository);

        $searchResultNotVisible = $searchService->findLocations($this->getLocationQuery(false));

        self::assertSame(2, $searchResultNotVisible->totalCount);
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location2 */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location3 */
        $location2 = $searchResultNotVisible->searchHits[0]->valueObject;
        $location3 = $searchResultNotVisible->searchHits[1]->valueObject;
        self::assertSame($contentB->contentInfo->mainLocationId, $location2->id);
        self::assertSame($additionalLocation->id, $location3->id);

        $searchResultVisible = $searchService->findLocations($this->getLocationQuery(true));

        self::assertSame(1, $searchResultVisible->totalCount);

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location1 */
        $location1 = $searchResultVisible->searchHits[0]->valueObject;
        self::assertSame($contentA->contentInfo->mainLocationId, $location1->id);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testFindInvisibleChildAdditionalSubtreeHiddenChildContent(): void
    {
        $repository = $this->getRepository();
        $searchService = $repository->getSearchService();
        $contentService = $repository->getContentService();
        $locationService = $repository->getLocationService();
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentA */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentB */
        [$contentA, $contentB] = $this->prepareTestFixtures();

        $contentService->hideContent($contentB->contentInfo);
        $locationCreateStruct = $locationService->newLocationCreateStruct(2);
        $additionalLocation1 = $locationService->createLocation($contentB->contentInfo, $locationCreateStruct);
        $locationCreateStruct = $locationService->newLocationCreateStruct($additionalLocation1->id);
        $additionalLocation2 = $locationService->createLocation($contentA->contentInfo, $locationCreateStruct);
        $this->refreshSearch($repository);

        $searchResultNotVisible = $searchService->findLocations($this->getLocationQuery(false));

        self::assertSame(3, $searchResultNotVisible->totalCount);

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location2 */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location3 */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location4 */
        $location2 = $searchResultNotVisible->searchHits[0]->valueObject;
        $location3 = $searchResultNotVisible->searchHits[1]->valueObject;
        $location4 = $searchResultNotVisible->searchHits[2]->valueObject;
        self::assertSame($contentB->contentInfo->mainLocationId, $location2->id);
        self::assertSame($additionalLocation1->id, $location3->id);
        self::assertSame($additionalLocation2->id, $location4->id);

        $searchResultVisible = $searchService->findLocations($this->getLocationQuery(true));

        self::assertSame(1, $searchResultVisible->totalCount);

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location1 */
        $location1 = $searchResultVisible->searchHits[0]->valueObject;
        self::assertSame($contentA->contentInfo->mainLocationId, $location1->id);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testFindInvisibleChildAdditionalSubtreeHiddenChildContentVariant(): void
    {
        $repository = $this->getRepository();
        $searchService = $repository->getSearchService();
        $contentService = $repository->getContentService();
        $locationService = $repository->getLocationService();
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentA */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentB */
        [$contentA, $contentB] = $this->prepareTestFixtures();

        $locationCreateStruct = $locationService->newLocationCreateStruct(2);
        $additionalLocation1 = $locationService->createLocation($contentB->contentInfo, $locationCreateStruct);
        $locationCreateStruct = $locationService->newLocationCreateStruct($additionalLocation1->id);
        $additionalLocation2 = $locationService->createLocation($contentA->contentInfo, $locationCreateStruct);
        $contentService->hideContent($contentB->contentInfo);
        $this->refreshSearch($repository);

        $searchResultNotVisible = $searchService->findLocations($this->getLocationQuery(false));

        self::assertSame(3, $searchResultNotVisible->totalCount);

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location2 */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location3 */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location4 */
        $location2 = $searchResultNotVisible->searchHits[0]->valueObject;
        $location3 = $searchResultNotVisible->searchHits[1]->valueObject;
        $location4 = $searchResultNotVisible->searchHits[2]->valueObject;
        self::assertSame($contentB->contentInfo->mainLocationId, $location2->id);
        self::assertSame($additionalLocation1->id, $location3->id);
        self::assertSame($additionalLocation2->id, $location4->id);

        $searchResultVisible = $searchService->findLocations($this->getLocationQuery(true));

        self::assertSame(1, $searchResultVisible->totalCount);

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location1 */
        $location1 = $searchResultVisible->searchHits[0]->valueObject;
        self::assertSame($contentA->contentInfo->mainLocationId, $location1->id);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function prepareTestFixtures(): array
    {
        $repository = $this->getRepository();
        $contentService = $repository->getContentService();
        $contentTypeService = $repository->getContentTypeService();
        $locationService = $repository->getLocationService();

        $contentTypeGroups = $contentTypeService->loadContentTypeGroups();
        $contentTypeCreateStruct = $contentTypeService->newContentTypeCreateStruct('stump');
        $contentTypeCreateStruct->mainLanguageCode = 'eng-GB';
        $contentTypeCreateStruct->names = ['eng-GB' => 'Stump type'];
        $fieldDefinitionCreateStruct = $contentTypeService->newFieldDefinitionCreateStruct('width', 'ibexa_integer');
        $contentTypeCreateStruct->addFieldDefinition($fieldDefinitionCreateStruct);
        $contentTypeDraft = $contentTypeService->createContentType($contentTypeCreateStruct, [reset($contentTypeGroups)]);
        $contentTypeService->publishContentTypeDraft($contentTypeDraft);
        $contentType = $contentTypeService->loadContentTypeByIdentifier('stump');

        $locationCreateStruct = $locationService->newLocationCreateStruct(2);

        $contentCreateStruct = $contentService->newContentCreateStruct($contentType, 'eng-GB');
        $contentCreateStruct->setField('width', 135);
        $contentDraft = $contentService->createContent($contentCreateStruct, [$locationCreateStruct]);
        $contentA = $contentService->publishVersion($contentDraft->versionInfo);

        $mainLocation = $contentA->contentInfo->getMainLocation();
        $locationCreateStruct = $locationService->newLocationCreateStruct($mainLocation->id);

        $contentCreateStruct = $contentService->newContentCreateStruct($contentType, 'eng-GB');
        $contentCreateStruct->setField('width', 235);
        $contentDraft = $contentService->createContent($contentCreateStruct, [$locationCreateStruct]);
        $contentB = $contentService->publishVersion($contentDraft->versionInfo);

        return [$contentA, $contentB];
    }

    protected function getContentQuery(bool $visible): Query
    {
        return new Query([
            'filter' => new LogicalAnd([
                new ContentTypeIdentifier('stump'),
                new Visible($visible),
            ]),
            'sortClauses' => [
                new ContentId(Query::SORT_ASC),
            ],
        ]);
    }

    protected function getLocationQuery(bool $visible): LocationQuery
    {
        return new LocationQuery([
            'filter' => new LogicalAnd([
                new ContentTypeIdentifier('stump'),
                new Visible($visible),
            ]),
            'sortClauses' => [
                new LocationId(Query::SORT_ASC),
            ],
        ]);
    }
}
