<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\TwigComponents\Renderer;

interface RendererInterface
{
    /**
     * @param array<mixed> $parameters
     *
     * @return string[]
     */
    public function renderGroup(string $groupName, array $parameters = []): array;

    /**
     * @param array<mixed> $parameters
     */
    public function renderSingle(string $groupName, string $name, array $parameters = []): string;
}
