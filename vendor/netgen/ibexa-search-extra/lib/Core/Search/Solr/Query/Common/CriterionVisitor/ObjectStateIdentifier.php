<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Solr\Query\Common\CriterionVisitor;

use Ibexa\Contracts\Core\Persistence\Content\ObjectState\Handler;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\ObjectStateIdentifier as ObjectStateIdentifierCriterion;

/**
 * Visits the ObjectStateIdentifier criterion.
 *
 * @see \Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\ObjectStateIdentifier
 */
final class ObjectStateIdentifier extends CriterionVisitor
{
    protected Handler $objectStateHandler;

    public function __construct(Handler $objectStateHandler)
    {
        $this->objectStateHandler = $objectStateHandler;
    }

    /**
     * Check if a visitor is applicable to the current criterion.
     */
    public function canVisit(CriterionInterface $criterion): bool
    {
        return
            $criterion instanceof ObjectStateIdentifierCriterion
            && $criterion->operator === Operator::EQ;
    }

    /**
     * Map field value to a proper Solr representation.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException If ObjectStateGroup or ObjectState is not found
     */
    public function visit(CriterionInterface $criterion, ?CriterionVisitor $subVisitor = null): string
    {
        $stateIdentifier = $criterion->value[0];
        $groupId = $this->objectStateHandler->loadGroupByIdentifier($criterion->target)->id;
        $stateId = $this->objectStateHandler->loadByIdentifier($stateIdentifier, $groupId)->id;

        return 'content_object_state_ids_mid:"' . $stateId . '"';
    }
}
