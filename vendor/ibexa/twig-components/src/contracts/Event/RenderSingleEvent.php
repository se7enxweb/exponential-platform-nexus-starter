<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\TwigComponents\Event;

use Ibexa\Contracts\TwigComponents\ComponentInterface;
use Ibexa\Contracts\TwigComponents\ComponentRegistryInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class RenderSingleEvent extends Event
{
    private ComponentRegistryInterface $registry;

    private string $groupName;

    private string $serviceId;

    /**
     * @var array<mixed>
     */
    private array $parameters;

    /**
     * @param array<mixed> $parameters
     */
    public function __construct(
        ComponentRegistryInterface $registry,
        string $groupName,
        string $serviceId,
        array $parameters = []
    ) {
        $this->registry = $registry;
        $this->groupName = $groupName;
        $this->serviceId = $serviceId;
        $this->parameters = $parameters;
    }

    public function getGroupName(): string
    {
        return $this->groupName;
    }

    public function getName(): string
    {
        return $this->serviceId;
    }

    public function getComponent(): ComponentInterface
    {
        $group = $this->registry->getComponents($this->getGroupName());

        return $group[$this->serviceId];
    }

    public function setComponent(ComponentInterface $component): void
    {
        $this->registry->addComponent($this->getGroupName(), $this->getName(), $component);
    }

    /**
     * @return array<mixed>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param mixed $value
     */
    public function addParameter(string $key, $value): void
    {
        $this->parameters[$key] = $value;
    }

    /**
     * @param array<mixed> $parameters
     */
    public function addParameters(array $parameters): void
    {
        $this->parameters = array_merge($this->parameters, $parameters);
    }
}
