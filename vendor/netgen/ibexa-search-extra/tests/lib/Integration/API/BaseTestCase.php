<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Tests\Integration\API;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Tests\Integration\Core\Repository\BaseTestCase as APIBaseTestCase;
use RuntimeException;
use function count;
use function get_class;

abstract class BaseTestCase extends APIBaseTestCase
{
    protected function assertSearchResultContentIds(
        SearchResult $searchResult,
        array $expectedIds,
        $totalCount = null
    ): void {
        $totalCount = $totalCount ?: count($expectedIds);
        self::assertEquals($totalCount, $searchResult->totalCount);

        $foundIds = [];

        foreach ($searchResult->searchHits as $searchHit) {
            $value = $searchHit->valueObject;

            if ($value instanceof ContentInfo) {
                $foundIds[] = $value->id;
            } elseif ($value instanceof Location) {
                $foundIds[] = $value->contentId;
            } else {
                throw new RuntimeException(
                    'Unknown value type: ' . get_class($value),
                );
            }
        }

        self::assertEquals($expectedIds, $foundIds);
    }

    protected function assertSearchResultLocationIds(
        SearchResult $searchResult,
        array $expectedIds,
        $totalCount = null
    ): void {
        $totalCount = $totalCount ?: count($expectedIds);
        self::assertEquals($totalCount, $searchResult->totalCount);

        $foundIds = [];

        foreach ($searchResult->searchHits as $searchHit) {
            $value = $searchHit->valueObject;

            if ($value instanceof ContentInfo) {
                $foundIds[] = $value->mainLocationId;
            } elseif ($value instanceof Location) {
                $foundIds[] = $value->id;
            } else {
                throw new RuntimeException(
                    'Unknown value type: ' . get_class($value),
                );
            }
        }

        self::assertEquals($expectedIds, $foundIds);
    }

    protected function getSearchService($initialInitializeFromScratch = true): SearchService
    {
        return $this->getRepository($initialInitializeFromScratch)->getSearchService();
    }
}
