<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\API\Values\Content\Query\SortClause;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;

/**
 * Sets sort direction on matched translation's Content name.
 */
final class ContentName extends SortClause
{
    public function __construct(string $sortDirection = Query::SORT_ASC)
    {
        parent::__construct('translated_content_name', $sortDirection);
    }
}
