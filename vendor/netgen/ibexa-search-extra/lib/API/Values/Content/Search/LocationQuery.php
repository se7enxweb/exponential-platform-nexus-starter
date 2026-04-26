<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\API\Values\Content\Search;

use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery as BaseLocationQuery;

class LocationQuery extends BaseLocationQuery
{
    /**
     * List of additional fields that should be
     * extracted from the Solr document for each hit.
     *
     * @var string[]
     */
    public array $extraFields;
}
