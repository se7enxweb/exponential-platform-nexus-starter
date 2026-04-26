<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\TwigComponents\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class AsTwigComponent
{
    public function __construct(
        public string $group,
        public int $priority = 0,
    ) {
    }
}
