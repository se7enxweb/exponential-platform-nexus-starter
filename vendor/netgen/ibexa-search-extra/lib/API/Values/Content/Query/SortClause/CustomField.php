<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\API\Values\Content\Query\SortClause;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;

/**
 * CustomField sort clause is used to sort Content by custom field indexed for this content.
 */
final class CustomField extends SortClause
{
    /**
     * @param string $fieldName
     * @param string $sortDirection
     */
    public function __construct(string $fieldName, string $sortDirection = Query::SORT_ASC)
    {
        parent::__construct($fieldName, $sortDirection);
    }
}
