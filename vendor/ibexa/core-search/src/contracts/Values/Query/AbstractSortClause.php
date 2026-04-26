<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CoreSearch\Values\Query;

use InvalidArgumentException;

abstract class AbstractSortClause
{
    /** @final */
    public const string SORT_ASC = SortDirection::ASC;

    /** @final */
    public const string SORT_DESC = SortDirection::DESC;

    /**
     * Sort direction.
     *
     * @phpstan-var SortDirection::*
     */
    private string $direction = SortDirection::ASC;

    /**
     * Constructs a new SortClause on $sortTarget in direction $sortDirection.
     *
     * @param string $sortDirection one of SortDirection::ASC or SortDirection::DESC
     */
    public function __construct(
        readonly string $sortDirection = self::SORT_ASC
    ) {
        $this->setDirection($sortDirection);
    }

    final public function getDirection(): string
    {
        return $this->direction;
    }

    /**
     * @throws \InvalidArgumentException if the given sort direction is invalid
     */
    final public function setDirection(string $direction): void
    {
        if (!SortDirection::isValid($direction)) {
            throw new InvalidArgumentException(sprintf(
                'Sort direction must be one of %1$s::ASC or %1$s::DESC',
                SortDirection::class,
            ));
        }

        /** @var SortDirection::* $direction */
        $this->direction = $direction;
    }
}
