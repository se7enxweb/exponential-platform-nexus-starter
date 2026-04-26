<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Solr\Query\Content\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\SubdocumentQuery as SubdocumentQueryCriterion;

use function str_replace;

/**
 * Visits the SubdocumentQuery criterion.
 *
 * @see \Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\SubdocumentQuery
 */
final class SubdocumentQuery extends CriterionVisitor
{
    private CriterionVisitor $subdocumentQueryCriterionVisitor;

    public function __construct(CriterionVisitor $subdocumentQueryCriterionVisitor)
    {
        $this->subdocumentQueryCriterionVisitor = $subdocumentQueryCriterionVisitor;
    }

    public function canVisit(CriterionInterface $criterion): bool
    {
        return $criterion instanceof SubdocumentQueryCriterion;
    }

    public function visit(CriterionInterface $criterion, ?CriterionVisitor $subVisitor = null): string
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion $query */
        $query = $criterion->value;
        $identifier = $criterion->target;

        $condition = $this->escapeQuote(
            $this->subdocumentQueryCriterionVisitor->visit($query),
        );

        $condition = str_replace('/', '\\/', $condition);

        return "{!parent which='document_type_id:content' v='document_type_id:{$identifier} AND {$condition}'}";
    }
}
