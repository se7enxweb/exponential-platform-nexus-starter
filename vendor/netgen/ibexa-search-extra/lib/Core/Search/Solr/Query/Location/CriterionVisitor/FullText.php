<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Solr\Query\Location\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Netgen\IbexaSearchExtra\Core\Search\Solr\Query\Content\CriterionVisitor\FullText as ContentFullText;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;

use function sprintf;

final class FullText extends CriterionVisitor
{
    public function __construct(private readonly ContentFullText $innerVisitor) {}

    public function canVisit(CriterionInterface $criterion): bool
    {
        return $this->innerVisitor->canVisit($criterion);
    }

    public function visit(CriterionInterface $criterion, ?CriterionVisitor $subVisitor = null): string
    {
        $condition = $this->escapeQuote($this->innerVisitor->visit($criterion, $subVisitor));
        return sprintf("{!child of='document_type_id:content' v='document_type_id:content AND %s'}", $condition);
    }
}
