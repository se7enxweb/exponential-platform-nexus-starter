<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Pagination\Pagerfanta;

use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Contracts\Core\Search\Handler as SearchHandlerInterface;

class SearchHandlerAdapter extends BaseAdapter
{
    private SearchHandlerInterface $searchHandler;

    public function __construct(Query $query, SearchHandlerInterface $searchHandler)
    {
        parent::__construct($query);

        $this->searchHandler = $searchHandler;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    protected function executeQuery(Query $query): SearchResult
    {
        if ($query instanceof LocationQuery) {
            return $this->searchHandler->findLocations($query);
        }

        return $this->searchHandler->findContent($query);
    }
}
