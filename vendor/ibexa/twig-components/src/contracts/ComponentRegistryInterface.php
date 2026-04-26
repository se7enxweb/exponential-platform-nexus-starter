<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\TwigComponents;

interface ComponentRegistryInterface
{
    public function addComponent(string $group, string $serviceId, ComponentInterface $component): void;

    /**
     * @return \Ibexa\Contracts\TwigComponents\ComponentInterface[]
     */
    public function getComponents(string $group): array;

    /**
     * @param \Ibexa\Contracts\TwigComponents\ComponentInterface[] $components
     */
    public function setComponents(string $group, array $components): void;
}
