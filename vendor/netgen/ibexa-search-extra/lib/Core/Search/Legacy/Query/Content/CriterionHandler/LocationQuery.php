<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Legacy\Query\Content\CriterionHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\Core\Persistence\Content\ContentInfo;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Ibexa\Core\Persistence\Legacy\Content\Gateway as ContentGateway;
use Ibexa\Core\Persistence\Legacy\Content\Location\Gateway;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\LocationQuery as LocationQueryCriterion;

/**
 * Handles the LocationQuery criterion.
 *
 * @see \Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\LocationQuery
 */
final class LocationQuery extends CriterionHandler
{
    /**
     * @var \Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter
     */
    private $locationCriteriaConverter;

    public function __construct(Connection $connection, CriteriaConverter $locationCriteriaConverter)
    {
        parent::__construct($connection);

        $this->locationCriteriaConverter = $locationCriteriaConverter;
    }

    public function accept(CriterionInterface $criterion)
    {
        return $criterion instanceof LocationQueryCriterion;
    }

    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        CriterionInterface $criterion,
        array $languageSettings
    ) {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion $filter */
        $filter = $criterion->value;
        $subSelect = new SubSelectQueryBuilder($this->connection, $queryBuilder);
        $condition = $this->locationCriteriaConverter->convertCriteria($subSelect, $filter, []);

        $subSelect
            ->select('t.contentobject_id')
            ->from(Gateway::CONTENT_TREE_TABLE, 't')
            ->innerJoin(
                't',
                ContentGateway::CONTENT_ITEM_TABLE,
                't2',
                't.contentobject_id = t2.id',
            )
            ->innerJoin(
                't2',
                ContentGateway::CONTENT_VERSION_TABLE,
                't3',
                't2.id = t3.contentobject_id',
            )
            ->where(
                $subSelect->expr()->andX(
                    $condition,
                    $subSelect->expr()->eq(
                        't2.status',
                        $queryBuilder->createNamedParameter(ContentInfo::STATUS_PUBLISHED, Types::INTEGER),
                    ),
                    $subSelect->expr()->eq(
                        't3.status',
                        $queryBuilder->createNamedParameter(VersionInfo::STATUS_PUBLISHED, Types::INTEGER),
                    ),
                ),
            );

        return $queryBuilder->expr()->in(
            'c.id',
            $subSelect->getSQL(),
        );
    }
}
