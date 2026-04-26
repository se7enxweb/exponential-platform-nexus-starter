<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Solr\Query\Common\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\ContentName;

/**
 * Visits the ContentName criterion.
 *
 * @see \Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\ContentName
 */
class ContentNameLike extends CriterionVisitor
{
    public function canVisit(CriterionInterface $criterion): bool
    {
        return $criterion instanceof ContentName && $criterion->operator === Operator::LIKE;
    }

    public function visit(CriterionInterface $criterion, ?CriterionVisitor $subVisitor = null): string
    {
        $value = $criterion->value[0];

        if (mb_strpos($value, '*') !== false) {
            return 'ng_content_name_s:' . $this->escapeExpressions($value, true);
        }

        return 'ng_content_name_s:"' . $this->escapeQuote($value) . '"';
    }
}
