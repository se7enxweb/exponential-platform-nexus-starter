<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Legacy\Query\Content\CriterionHandler;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Core\Persistence\Legacy\Content\Location\Gateway;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\LocationId as LocationIdCriterion;
use RuntimeException;
use function reset;

/**
 * @see \Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\LocationId
 */
final class LocationId extends CriterionHandler
{
    public function accept(CriterionInterface $criterion)
    {
        return $criterion instanceof LocationIdCriterion;
    }

    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        CriterionInterface $criterion,
        array $languageSettings
    ) {
        $column = 'node_id';
        $subSelect = $this->connection->createQueryBuilder();

        switch ($criterion->operator) {
            case Operator::EQ:
            case Operator::IN:
                $expression = $queryBuilder->expr()->in($column, $criterion->value);

                break;

            case Operator::GT:
            case Operator::GTE:
            case Operator::LT:
            case Operator::LTE:
                $operatorFunction = $this->comparatorMap[$criterion->operator];
                $expression = $queryBuilder->expr()->{$operatorFunction}(
                    $column,
                    $queryBuilder->createNamedParameter(reset($criterion->value), ParameterType::INTEGER)
                );

                break;

            case Operator::BETWEEN:
                $expression = $this->dbPlatform->getBetweenExpression(
                    $column,
                    $queryBuilder->createNamedParameter($criterion->value[0], ParameterType::INTEGER),
                    $queryBuilder->createNamedParameter($criterion->value[1], ParameterType::INTEGER),
                );

                break;

            default:
                throw new RuntimeException(
                    "Unknown operator '{$criterion->operator}' for LocationId criterion handler.",
                );
        }

        $subSelect->select('contentobject_id')->from(Gateway::CONTENT_TREE_TABLE)->where($expression);

        return $queryBuilder->expr()->in('c.id', $subSelect->getSQL());
    }
}
