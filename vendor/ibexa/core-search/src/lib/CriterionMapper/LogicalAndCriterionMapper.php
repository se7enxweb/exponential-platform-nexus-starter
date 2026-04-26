<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CoreSearch\CriterionMapper;

use Doctrine\Common\Collections\Expr\CompositeExpression;
use Ibexa\Contracts\CoreSearch\Persistence\CriterionMapper\AbstractCompositeCriterionMapper;
use Ibexa\Contracts\CoreSearch\Values\Query\Criterion\LogicalAnd;

final class LogicalAndCriterionMapper extends AbstractCompositeCriterionMapper
{
    protected function getHandledClass(): string
    {
        return LogicalAnd::class;
    }

    protected function getType(): string
    {
        return CompositeExpression::TYPE_AND;
    }
}
