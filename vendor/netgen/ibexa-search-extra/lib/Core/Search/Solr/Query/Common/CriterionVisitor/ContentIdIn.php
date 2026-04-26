<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Solr\Query\Common\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\ContentId as ContentIdCriterion;

use function implode;

/**
 * Visits the ContentId criterion.
 *
 * @see \Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\ContentId
 */
final class ContentIdIn extends CriterionVisitor
{
    public function canVisit(CriterionInterface $criterion): bool
    {
        return
            $criterion instanceof ContentIdCriterion
            && (
                $criterion->operator === Operator::IN
                || $criterion->operator === Operator::EQ
            );
    }

    public function visit(CriterionInterface $criterion, ?CriterionVisitor $subVisitor = null): string
    {
        $values = [];

        foreach ($criterion->value as $value) {
            $values[] = 'ng_content_id_i:"' . $value . '"';
        }

        return '(' . implode(' OR ', $values) . ')';
    }
}
