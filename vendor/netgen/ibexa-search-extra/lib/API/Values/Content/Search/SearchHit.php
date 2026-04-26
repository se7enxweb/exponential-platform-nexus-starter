<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\API\Values\Content\Search;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit as BaseSearchHit;

class SearchHit extends BaseSearchHit
{
    /**
     * Additional fields from Solr document.
     */
    public array $extraFields;
}
