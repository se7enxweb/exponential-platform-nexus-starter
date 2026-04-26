<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Pagination\Pagerfanta;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;

class SearchAdapter extends BaseAdapter
{
    private SearchService $searchService;

    public function __construct(Query $query, SearchService $searchService)
    {
        parent::__construct($query);

        $this->searchService = $searchService;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    protected function executeQuery(Query $query): SearchResult
    {
        if ($query instanceof LocationQuery) {
            return $this->searchService->findLocations($query);
        }

        return $this->searchService->findContent($query);
    }
}
