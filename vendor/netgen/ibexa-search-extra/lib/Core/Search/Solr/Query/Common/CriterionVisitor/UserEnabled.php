<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Solr\Query\Common\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\UserEnabled as UserEnabledCriterion;

class UserEnabled extends CriterionVisitor
{
    public function canVisit(CriterionInterface $criterion): bool
    {
        return $criterion instanceof UserEnabledCriterion;
    }

    public function visit(CriterionInterface $criterion, ?CriterionVisitor $subVisitor = null): string
    {
        $isEnabled = $criterion->value[0];

        return 'ng_user_enabled_b:' . ($isEnabled ? 'true' : 'false');
    }
}
