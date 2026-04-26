<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\TwigComponents\DataCollector;

use Ibexa\Contracts\TwigComponents\ComponentInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Throwable;

final class TwigComponentCollector extends DataCollector
{
    /** @var array<int, mixed> */
    private array $renderedComponents = [];

    /** @var array<int, mixed> */
    private array $availableGroups = [];

    public function addRenderedComponent(string $group, string $name, ComponentInterface $component): void
    {
        $componentClass = get_parent_class($component) ?: get_class($component);

        $this->renderedComponents[] = compact('group', 'name', 'componentClass');
    }

    public function addAvailableGroups(string $group): void
    {
        $this->availableGroups[] = compact('group');
    }

    public function collect(Request $request, Response $response, ?Throwable $exception = null): void
    {
        $this->data['rendered_components'] = $this->renderedComponents;
        $this->data['available_groups'] = $this->availableGroups;
    }

    public function reset(): void
    {
        $this->renderedComponents = [];
        $this->data = [];
    }

    /**
     * @return array<int, mixed>
     */
    public function getRenderedComponents(): array
    {
        return $this->data['rendered_components'] ?? [];
    }

    /**
     * @return array<int, mixed>
     */
    public function getAvailableGroups(): array
    {
        return $this->data['available_groups'] ?? [];
    }

    public function getName(): string
    {
        return 'ibexa.twig_components';
    }
}
