<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Solr\Query\Content\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\LocationId as LocationIdCriterion;

/**
 * Visits the LocationId criterion.
 *
 * @see \Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\LocationId
 */
class LocationIdBetween extends CriterionVisitor
{
    public function canVisit(CriterionInterface $criterion): bool
    {
        return
            $criterion instanceof LocationIdCriterion
            && (
                $criterion->operator === Operator::LT
                || $criterion->operator === Operator::LTE
                || $criterion->operator === Operator::GT
                || $criterion->operator === Operator::GTE
                || $criterion->operator === Operator::BETWEEN
            );
    }

    public function visit(CriterionInterface $criterion, ?CriterionVisitor $subVisitor = null): string
    {
        $start = $criterion->value[0];
        $end = $criterion->value[1] ?? null;

        if ($criterion->operator === Operator::LT || $criterion->operator === Operator::LTE) {
            $end = $start;
            $start = null;
        }

        return 'ng_location_id_mi:' . $this->getRange($criterion->operator, $start, $end);
    }
}
