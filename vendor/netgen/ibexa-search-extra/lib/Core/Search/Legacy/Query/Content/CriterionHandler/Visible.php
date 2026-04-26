<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Legacy\Query\Content\CriterionHandler;

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
        $isHiddenInteger = (int) !$criterion->value[0];

        return $queryBuilder->expr()->eq(
            'c.is_hidden',
            $queryBuilder->createNamedParameter($isHiddenInteger, ParameterType::INTEGER),
        );
    }
}
