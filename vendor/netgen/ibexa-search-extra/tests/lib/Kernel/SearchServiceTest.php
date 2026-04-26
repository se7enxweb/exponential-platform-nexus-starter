<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Tests\Kernel;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit as KernelSearchHit;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult as KernelSearchResult;
use Ibexa\Tests\Integration\Core\Repository\SearchServiceTest as KernelSearchServiceTest;
use Netgen\IbexaSearchExtra\API\Values\Content\Search\SearchHit;
use Netgen\IbexaSearchExtra\API\Values\Content\Search\SearchResult;
use function is_callable;

class SearchServiceTest extends KernelSearchServiceTest
{
    protected function assertQueryFixture(
        Query $query,
        string $fixtureFilePath,
        ?callable $closure = null,
        bool $ignoreScore = true,
        bool $info = false,
        bool $id = true
    ): void {
        $newClosure = function (&$data) use ($closure) {
            if ($data instanceof SearchResult) {
                $data = $this->mapToKernelSearchResult($data);
            }

            if (is_callable($closure)) {
                $closure($data);
            }
        };

        parent::assertQueryFixture($query, $fixtureFilePath, $newClosure, $ignoreScore, $info, $id);
    }

    private function mapToKernelSearchResult(SearchResult $data): KernelSearchResult
    {
        $kernelSearchHits = [];

        /** @var \Netgen\IbexaSearchExtra\API\Values\Content\Search\SearchHit $searchHit */
        foreach ($data->searchHits as $searchHit) {
            $kernelSearchHits[] = $this->mapToKernelSearchHit($searchHit);
        }

        return new KernelSearchResult([
            'searchHits' => $kernelSearchHits,
            'spellcheck' => $data->spellcheck,
            'time' => $data->time,
            'timedOut' => $data->timedOut,
            'maxScore' => $data->maxScore,
            'totalCount' => $data->totalCount,
        ]);
    }

    private function mapToKernelSearchHit(SearchHit $searchHit): KernelSearchHit
    {
        return new KernelSearchHit([
            'valueObject' => $searchHit->valueObject,
            'score' => $searchHit->score,
            'index' => $searchHit->index,
            'matchedTranslation' => $searchHit->matchedTranslation,
            'highlight' => $searchHit->highlight,
        ]);
    }
}
