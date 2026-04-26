<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CoreSearch\Values\Query\SortClause;

use Ibexa\Contracts\CoreSearch\Values\Query\AbstractSortClause;

class FieldValueSortClause extends AbstractSortClause
{
    public function __construct(
        private readonly string $field,
        string $sortDirection = self::SORT_ASC
    ) {
        parent::__construct($sortDirection);
    }

    public function getField(): string
    {
        return $this->field;
    }
}
