<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Legacy\Query\Common\CriterionHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Persistence\Content\Section\Handler as SectionHandler;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\SectionIdentifier as SectionIdentifierCriterion;

/**
 * Handles the SectionIdentifier criterion.
 *
 * @see \Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\SectionIdentifier
 */
final class SectionIdentifier extends CriterionHandler
{
    /**
     * @var \Ibexa\Contracts\Core\Persistence\Content\Section\Handler
     */
    protected $sectionHandler;

    public function __construct(Connection $connection, SectionHandler $sectionHandler)
    {
        parent::__construct($connection);

        $this->sectionHandler = $sectionHandler;
    }

    public function accept(CriterionInterface $criterion)
    {
        return $criterion instanceof SectionIdentifierCriterion;
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
        $ids = [];

        foreach ($criterion->value as $identifier) {
            $ids[] = $this->sectionHandler->loadByIdentifier($identifier)->id;
        }

        return $queryBuilder->expr()->in(
            'c.section_id',
            $queryBuilder->createNamedParameter($ids, Connection::PARAM_INT_ARRAY),
        );
    }
}
