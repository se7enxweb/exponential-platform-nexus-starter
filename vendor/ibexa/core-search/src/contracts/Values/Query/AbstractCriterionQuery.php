<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CoreSearch\Values\Query;

use Ibexa\Contracts\CoreSearch\Values\Query\Criterion\CriterionInterface;

/**
 * @template TSortClause of \Ibexa\Contracts\CoreSearch\Values\Query\AbstractSortClause
 * @template TCriterion of \Ibexa\Contracts\CoreSearch\Values\Query\Criterion\CriterionInterface
 */
abstract class AbstractCriterionQuery implements PaginationAwareInterface
{
    public const int DEFAULT_LIMIT = 25;

    /**
     * @var array<int, AbstractSortClause>
     *
     * @phpstan-var TSortClause[]
     */
    private array $sortClauses;

    /**
     * @param array<int, AbstractSortClause>|null $sortClauses
     *
     * @phpstan-param TSortClause[]|null $sortClauses
     * @phpstan-param TCriterion|null $query
     */
    public function __construct(
        private ?CriterionInterface $query = null,
        ?array $sortClauses = [],
        private ?int $limit = self::DEFAULT_LIMIT,
        private int $offset = 0
    ) {
        $this->sortClauses = $sortClauses ?? [];
    }

    /**
     * @phpstan-param TCriterion|null $criterion
     */
    final public function setQuery(?CriterionInterface $criterion): void
    {
        $this->query = $criterion;
    }

    /**
     * @phpstan-return TCriterion|null
     */
    final public function getQuery(): ?CriterionInterface
    {
        return $this->query;
    }

    final public function hasQuery(): bool
    {
        return $this->query !== null;
    }

    final public function getOffset(): int
    {
        return $this->offset;
    }

    final public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

    final public function getLimit(): ?int
    {
        return $this->limit;
    }

    final public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @return array<int, AbstractSortClause>
     *
     * @phpstan-return TSortClause[]
     */
    final public function getSortClauses(): array
    {
        return $this->sortClauses;
    }

    /**
     * @phpstan-param TSortClause $sortClause
     */
    final public function addSortClause(AbstractSortClause $sortClause): void
    {
        $this->sortClauses[] = $sortClause;
    }

    /**
     * @param array<int, AbstractSortClause> $sortClauses
     *
     * @phpstan-param TSortClause[] $sortClauses
     */
    final public function setSortClauses(array $sortClauses): void
    {
        $this->sortClauses = $sortClauses;
    }
}
