<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CoreSearch\CriterionMapper;

use Ibexa\Contracts\CoreSearch\Persistence\CriterionMapper\AbstractFieldCriterionMapper;
use Ibexa\Contracts\CoreSearch\Values\Query\Criterion\CriterionInterface;
use Ibexa\Contracts\CoreSearch\Values\Query\Criterion\FieldValueCriterion;

/**
 * @template-extends \Ibexa\Contracts\CoreSearch\Persistence\CriterionMapper\AbstractFieldCriterionMapper<
 *     \Ibexa\Contracts\CoreSearch\Values\Query\Criterion\FieldValueCriterion
 * >
 */
final class FieldValueCriterionMapper extends AbstractFieldCriterionMapper
{
    public function canHandle(CriterionInterface $criterion): bool
    {
        return $criterion instanceof FieldValueCriterion;
    }
}
