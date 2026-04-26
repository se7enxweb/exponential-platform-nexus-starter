<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\TwigComponents\Templating\Twig\Components\Table;

use Closure;

final readonly class Column
{
    /**
     * @phpstan-param Closure(Column): string $label
     * @phpstan-param Closure(mixed, Column): string $renderer
     */
    public function __construct(
        public string $identifier,
        public Closure $label,
        public Closure $renderer,
        public int $priority = 0,
    ) {
    }
}
