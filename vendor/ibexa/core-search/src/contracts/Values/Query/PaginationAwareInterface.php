<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CoreSearch\Values\Query;

interface PaginationAwareInterface
{
    public function getOffset(): int;

    public function getLimit(): ?int;
}
