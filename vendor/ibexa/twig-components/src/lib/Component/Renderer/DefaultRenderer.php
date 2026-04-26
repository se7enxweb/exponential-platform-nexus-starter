<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\TwigComponents\Component\Renderer;

use Ibexa\Contracts\TwigComponents\Event\RenderGroupEvent;
use Ibexa\Contracts\TwigComponents\Event\RenderSingleEvent;
use Ibexa\Contracts\TwigComponents\Exception\InvalidArgumentException;
use Ibexa\Contracts\TwigComponents\Renderer\RendererInterface;
use Ibexa\TwigComponents\Component\Registry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class DefaultRenderer implements RendererInterface
{
    private Registry $registry;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        Registry $registry,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->registry = $registry;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param array<mixed> $parameters
     *
     * @return string[]
     */
    public function renderGroup(string $groupName, array $parameters = []): array
    {
        $this->eventDispatcher->dispatch(new RenderGroupEvent(
            $this->registry,
            $groupName,
            $parameters
        ), RenderGroupEvent::class);

        $components = $this->registry->getComponents($groupName);

        $rendered = [];
        foreach ($components as $name => $component) {
            $rendered[] = $this->renderSingleWithComponents($groupName, $name, $components, $parameters);
        }

        return $rendered;
    }

    /**
     * @param array<mixed> $parameters
     */
    public function renderSingle(string $groupName, string $name, array $parameters = []): string
    {
        $components = $this->registry->getComponents($groupName);

        return $this->renderSingleWithComponents($groupName, $name, $components, $parameters);
    }

    /**
     * @param array<mixed> $parameters
     * @param array<string, \Ibexa\Contracts\TwigComponents\ComponentInterface> $components
     */
    private function renderSingleWithComponents(
        string $groupName,
        string $name,
        array $components,
        array $parameters = []
    ): string {
        $this->eventDispatcher->dispatch(new RenderSingleEvent(
            $this->registry,
            $groupName,
            $name,
            $parameters
        ), RenderSingleEvent::class);

        if (!isset($components[$name])) {
            throw new InvalidArgumentException(
                'id',
                sprintf("Can't find Component '%s' in group '%s'", $name, $groupName),
            );
        }

        return $components[$name]->render($parameters);
    }
}
