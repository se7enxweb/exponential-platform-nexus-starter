<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Solr\Query\Common\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\ContentName;

use function implode;

/**
 * Visits the ContentName criterion.
 *
 * @see \Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\ContentName
 */
final class ContentNameIn extends CriterionVisitor
{
    public function canVisit(CriterionInterface $criterion): bool
    {
        return
            $criterion instanceof ContentName
            && (
                $criterion->operator === Operator::IN
                || $criterion->operator === Operator::EQ
            );
    }

    public function visit(CriterionInterface $criterion, ?CriterionVisitor $subVisitor = null): string
    {
        $values = [];

        foreach ($criterion->value as $value) {
            $values[] = 'ng_content_name_s:"' . $this->escapeQuote($value, true) . '"';
        }

        return '(' . implode(' OR ', $values) . ')';
    }
}
