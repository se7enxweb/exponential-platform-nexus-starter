<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Legacy\Query\Location\CriterionHandler;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\Visible as VisibleCriterion;

class Visible extends CriterionHandler
{
    public function accept(CriterionInterface $criterion)
    {
        return $criterion instanceof VisibleCriterion;
    }

    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        CriterionInterface $criterion,
        array $languageSettings
    ) {
        $isVisible = $criterion->value[0];
        $expr = $queryBuilder->expr();

        if ($isVisible) {
            return $expr->andX(
                $expr->eq(
                    't.is_hidden',
                    $queryBuilder->createNamedParameter(0, ParameterType::INTEGER),
                ),
                $expr->eq(
                    't.is_invisible',
                    $queryBuilder->createNamedParameter(0, ParameterType::INTEGER),
                ),
                $expr->eq(
                    'c.is_hidden',
                    $queryBuilder->createNamedParameter(0, ParameterType::INTEGER),
                ),
            );
        }

        return $expr->orX(
            $expr->eq(
                't.is_hidden',
                $queryBuilder->createNamedParameter(1, ParameterType::INTEGER),
            ),
            $expr->eq(
                't.is_invisible',
                $queryBuilder->createNamedParameter(1, ParameterType::INTEGER),
            ),
            $expr->eq(
                'c.is_hidden',
                $queryBuilder->createNamedParameter(1, ParameterType::INTEGER),
            ),
        );
    }
}
