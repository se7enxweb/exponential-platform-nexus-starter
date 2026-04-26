<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\TwigComponents\Component;

use Ibexa\Contracts\TwigComponents\ComponentInterface;
use Twig\Environment;

class TemplateComponent implements ComponentInterface
{
    private Environment $twig;

    private string $template;

    /**
     * @var array<mixed>
     */
    private array $parameters;

    /**
     * @param array<mixed> $parameters
     */
    public function __construct(
        Environment $twig,
        string $template,
        array $parameters = []
    ) {
        $this->twig = $twig;
        $this->template = $template;
        $this->parameters = $parameters;
    }

    /**
     * @param array<mixed> $parameters
     */
    public function render(array $parameters = []): string
    {
        $parameters = $this->getParameters($parameters);

        return $this->twig->render($this->template, $parameters);
    }

    /**
     * @param array<mixed> $parameters
     *
     * @return array<mixed>
     */
    protected function getParameters(array $parameters): array
    {
        return $parameters + $this->parameters;
    }
}
