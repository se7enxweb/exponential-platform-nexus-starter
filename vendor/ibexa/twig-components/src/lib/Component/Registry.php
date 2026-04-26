<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\TwigComponents\Component;

use Ibexa\Contracts\TwigComponents\ComponentInterface;
use Ibexa\Contracts\TwigComponents\ComponentRegistryInterface;

final class Registry implements ComponentRegistryInterface
{
    /**
     * @var array<string, array<\Ibexa\Contracts\TwigComponents\ComponentInterface>>
     */
    private array $components;

    /**
     * @param array<string, array<\Ibexa\Contracts\TwigComponents\ComponentInterface>> $components
     */
    public function __construct(array $components = [])
    {
        $this->components = $components;
    }

    public function addComponent(string $group, string $serviceId, ComponentInterface $component): void
    {
        $this->components[$group][$serviceId] = $component;
    }

    /**
     * @return \Ibexa\Contracts\TwigComponents\ComponentInterface[]
     */
    public function getComponents(string $group): array
    {
        return $this->components[$group] ?? [];
    }

    /**
     * @param \Ibexa\Contracts\TwigComponents\ComponentInterface[] $components
     */
    public function setComponents(string $group, array $components): void
    {
        $this->components[$group] = $components;
    }
}
