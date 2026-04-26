<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Solr\Query\Content\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\LocationQuery as LocationQueryCriterion;

use function str_replace;

/**
 * Visits the LocationQuery criterion.
 *
 * @see \Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\LocationQuery
 */
final class LocationQuery extends CriterionVisitor
{
    private CriterionVisitor $locationQueryCriterionVisitor;

    public function __construct(CriterionVisitor $locationQueryCriterionVisitor)
    {
        $this->locationQueryCriterionVisitor = $locationQueryCriterionVisitor;
    }

    public function canVisit(CriterionInterface $criterion): bool
    {
        return $criterion instanceof LocationQueryCriterion;
    }

    public function visit(CriterionInterface $criterion, ?CriterionVisitor $subVisitor = null): string
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion $filter */
        $filter = $criterion->value;

        $condition = $this->escapeQuote(
            $this->locationQueryCriterionVisitor->visit($filter),
        );

        $condition = str_replace('/', '\\/', $condition);

        return "{!parent which='document_type_id:content' v='document_type_id:location AND {$condition}'}";
    }
}
