<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\TwigComponents\Component;

use Ibexa\Contracts\TwigComponents\ComponentInterface;
use Twig\Environment;

/**
 * This Twig Component allows to render menu.
 */
class MenuComponent implements ComponentInterface
{
    private Environment $twig;

    private string $name;

    /** @var array<string, mixed> */
    private array $options;

    /** @var string[] */
    private array $path;

    private ?string $template;

    private ?int $depth;

    /**
     * @param string $name Name of the menu
     * @param array<string, mixed> $options Options passed to menu builder
     * @param string[] $path Path to starting node
     * @param string|null $template Template to use for rendering the menu
     * @param int|null $depth Depth limit
     */
    public function __construct(
        Environment $twig,
        string $name,
        array $options = [],
        array $path = [],
        ?string $template = null,
        ?int $depth = null
    ) {
        $this->twig = $twig;
        $this->name = $name;
        $this->options = $options;
        $this->path = $path;
        $this->template = $template;
        $this->depth = $depth;
    }

    public function render(array $parameters = []): string
    {
        $rendererOptions = [];
        if ($this->template !== null) {
            $rendererOptions['template'] = $this->template;
        }

        if ($this->depth !== null) {
            $rendererOptions['depth'] = $this->depth;
        }

        return $this->twig->render(
            '@ibexadesign/twig_components/menu.html.twig',
            array_merge(
                [
                    'name' => $this->name,
                    'options' => $this->options,
                    'path' => $this->path,
                    'renderer_options' => $rendererOptions,
                ],
                $parameters
            )
        );
    }
}
