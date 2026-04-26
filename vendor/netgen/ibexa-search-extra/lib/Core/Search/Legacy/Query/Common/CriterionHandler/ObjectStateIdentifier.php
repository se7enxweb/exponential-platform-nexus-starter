<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Legacy\Query\Common\CriterionHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\Core\Persistence\Content\ObjectState\Handler as ObjectStateHandler;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Ibexa\Core\Persistence\Legacy\Content\ObjectState\Gateway;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\ObjectStateIdentifier as ObjectStateIdentifierCriterion;

/**
 * Handles the ObjectStateIdentifier criterion.
 *
 * @see \Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\ObjectStateIdentifier
 */
final class ObjectStateIdentifier extends CriterionHandler
{
    /**
     * @var \Ibexa\Contracts\Core\Persistence\Content\Section\Handler
     */
    protected $objectStateHandler;

    public function __construct(Connection $connection, ObjectStateHandler $objectStateHandler)
    {
        parent::__construct($connection);

        $this->objectStateHandler = $objectStateHandler;
    }

    public function accept(CriterionInterface $criterion)
    {
        return $criterion instanceof ObjectStateIdentifierCriterion;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        CriterionInterface $criterion,
        array $languageSettings
    ) {
        $stateIdentifier = $criterion->value[0];
        $groupId = $this->objectStateHandler->loadGroupByIdentifier($criterion->target)->id;
        $stateId = $this->objectStateHandler->loadByIdentifier($stateIdentifier, $groupId)->id;
        $subQuery = $this->connection->createQueryBuilder();

        $subQuery
            ->select('t1.contentobject_id')
            ->from(Gateway::OBJECT_STATE_LINK_TABLE, 't1')
            ->where(
                $subQuery->expr()->eq(
                    't1.contentobject_state_id',
                    $queryBuilder->createNamedParameter($stateId, Types::INTEGER),
                ),
            );

        return $queryBuilder->expr()->in(
            'c.id',
            $subQuery->getSQL(),
        );
    }
}
