<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CoreSearch\Values\Query\Criterion;

class FieldValueCriterion implements CriterionInterface
{
    /** @final */
    public const string COMPARISON_EQ = '=';
    /** @final */
    public const string COMPARISON_NEQ = '<>';
    /** @final */
    public const string COMPARISON_LT = '<';
    /** @final */
    public const string COMPARISON_LTE = '<=';
    /** @final */
    public const string COMPARISON_GT = '>';
    /** @final */
    public const string COMPARISON_GTE = '>=';
    /** @final */
    public const string COMPARISON_IN = 'IN';
    /** @final */
    public const string COMPARISON_NIN = 'NIN';
    /** @final */
    public const string COMPARISON_CONTAINS = 'CONTAINS';
    /** @final */
    public const string COMPARISON_MEMBER_OF = 'MEMBER_OF';
    /** @final */
    public const string COMPARISON_STARTS_WITH = 'STARTS_WITH';
    /** @final */
    public const string COMPARISON_ENDS_WITH = 'ENDS_WITH';

    private string $operator;

    public function __construct(
        private readonly string $field,
        private mixed $value,
        ?string $operator = null
    ) {
        $this->operator = $operator ?? (is_array($value) ? self::COMPARISON_IN : self::COMPARISON_EQ);
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setOperator(string $operator): void
    {
        $this->operator = $operator;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }
}
