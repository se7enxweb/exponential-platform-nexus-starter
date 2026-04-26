<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Solr\Query\Common\CriterionVisitor;

use Ibexa\Contracts\Core\Persistence\Content\Section\Handler;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\SectionIdentifier as SectionIdentifierCriterion;

use function array_map;
use function implode;

/**
 * Visits the SectionIdentifier criterion.
 *
 * @see \Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\SectionIdentifier
 */
final class SectionIdentifier extends CriterionVisitor
{
    protected Handler $sectionHandler;

    public function __construct(Handler $sectionHandler)
    {
        $this->sectionHandler = $sectionHandler;
    }

    public function canVisit(CriterionInterface $criterion): bool
    {
        return
            $criterion instanceof SectionIdentifierCriterion
            && (
                ($criterion->operator ?: Operator::IN) === Operator::IN
                || $criterion->operator === Operator::EQ
            );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function visit(CriterionInterface $criterion, ?CriterionVisitor $subVisitor = null): string
    {
        $handler = $this->sectionHandler;

        $conditions = array_map(
            static fn ($value) => 'content_section_id_id:"' . $handler->loadByIdentifier($value)->id . '"',
            $criterion->value,
        );

        return '(' . implode(' OR ', $conditions) . ')';
    }
}
