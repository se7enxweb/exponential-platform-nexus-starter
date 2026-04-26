<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CoreSearch\Values\Query;

use Doctrine\Common\Collections\Expr\Expression;
use Ibexa\Contracts\CoreSearch\Values\Query\Criterion\CriterionInterface;

/**
 * @template C of \Ibexa\Contracts\CoreSearch\Values\Query\Criterion\CriterionInterface
 */
interface CriterionMapperInterface
{
    public function canHandle(CriterionInterface $criterion): bool;

    /**
     * @phpstan-param C $criterion
     */
    public function handle(CriterionInterface $criterion, CriterionMapper $mapper): Expression;
}
