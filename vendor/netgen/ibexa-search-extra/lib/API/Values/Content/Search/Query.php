<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\API\Values\Content\Search;

use Ibexa\Contracts\Core\Repository\Values\Content\Query as BaseQuery;

class Query extends BaseQuery
{
    /**
     * List of additional fields that should be
     * extracted from the Solr document for each hit.
     *
     * @var string[]
     */
    public array $extraFields = [];
}
