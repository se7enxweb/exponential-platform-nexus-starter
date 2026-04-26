<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\API\Values\Content\Search;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult as BaseSearchResult;

class SearchResult extends BaseSearchResult
{
    /**
     * Contains suggestion for misspelled words.
     */
    public ?Suggestion $suggestion = null;
}
