<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\TwigComponents\Templating\Twig;

use Ibexa\Contracts\TwigComponents\Renderer\RendererInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ComponentExtension extends AbstractExtension
{
    private RendererInterface $renderer;

    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ibexa_twig_component_group',
                $this->renderComponentGroup(...),
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'ibexa_twig_component',
                $this->renderComponent(...),
                ['is_safe' => ['html']]
            ),
        ];
    }

    /**
     * @param array<mixed> $parameters
     */
    public function renderComponentGroup(string $group, array $parameters = []): string
    {
        return implode('', $this->renderer->renderGroup($group, $parameters));
    }

    /**
     * @param array<mixed> $parameters
     */
    public function renderComponent(string $group, string $id, array $parameters = []): string
    {
        return $this->renderer->renderSingle($group, $id, $parameters);
    }
}
