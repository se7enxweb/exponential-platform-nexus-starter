<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CoreSearch\Persistence\CriterionMapper;

use Doctrine\Common\Collections\Expr\Comparison;
use Ibexa\Contracts\CoreSearch\Values\Query\Criterion\CriterionInterface;
use Ibexa\Contracts\CoreSearch\Values\Query\Criterion\FieldValueCriterion;
use Ibexa\Contracts\CoreSearch\Values\Query\CriterionMapper;
use Ibexa\Contracts\CoreSearch\Values\Query\CriterionMapperInterface;
use LogicException;

/**
 * @template T of \Ibexa\Contracts\CoreSearch\Values\Query\Criterion\FieldValueCriterion
 *
 * @template-implements \Ibexa\Contracts\CoreSearch\Values\Query\CriterionMapperInterface<T>
 */
abstract class AbstractFieldCriterionMapper implements CriterionMapperInterface
{
    /**
     * @var array<
     *     \Ibexa\Contracts\CoreSearch\Values\Query\Criterion\FieldValueCriterion::COMPARISON_*,
     *     \Doctrine\Common\Collections\Expr\Comparison::*
     * >
     */
    private static array $comparisonMap = [
        FieldValueCriterion::COMPARISON_EQ => Comparison::EQ,
        FieldValueCriterion::COMPARISON_NEQ => Comparison::NEQ,
        FieldValueCriterion::COMPARISON_LT => Comparison::LT,
        FieldValueCriterion::COMPARISON_LTE => Comparison::LTE,
        FieldValueCriterion::COMPARISON_GT => Comparison::GT,
        FieldValueCriterion::COMPARISON_GTE => Comparison::GTE,
        FieldValueCriterion::COMPARISON_IN => Comparison::IN,
        FieldValueCriterion::COMPARISON_NIN => Comparison::NIN,
        FieldValueCriterion::COMPARISON_CONTAINS => Comparison::CONTAINS,
        FieldValueCriterion::COMPARISON_MEMBER_OF => Comparison::MEMBER_OF,
        FieldValueCriterion::COMPARISON_STARTS_WITH => Comparison::STARTS_WITH,
        FieldValueCriterion::COMPARISON_ENDS_WITH => Comparison::ENDS_WITH,
    ];

    /**
     * @phpstan-param T $criterion
     */
    final public function handle(CriterionInterface $criterion, CriterionMapper $mapper): Comparison
    {
        assert($criterion instanceof FieldValueCriterion);

        return new Comparison(
            $this->getComparisonField($criterion),
            $this->getComparisonOperator($criterion),
            $this->getComparisonValue($criterion)
        );
    }

    protected function getComparisonField(FieldValueCriterion $criterion): string
    {
        return $criterion->getField();
    }

    /**
     * @phpstan-return \Doctrine\Common\Collections\Expr\Comparison::*
     */
    protected function getComparisonOperator(FieldValueCriterion $criterion): string
    {
        $operator = $criterion->getOperator();
        if (isset(self::$comparisonMap[$operator])) {
            return self::$comparisonMap[$operator];
        }

        throw new LogicException(sprintf(
            'Unable to map %s operator %s to a valid DBAL operator',
            get_class($criterion),
            $operator,
        ));
    }

    protected function getComparisonValue(FieldValueCriterion $criterion): mixed
    {
        return $criterion->getValue();
    }
}
