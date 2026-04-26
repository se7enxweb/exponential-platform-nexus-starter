<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\TwigComponents\Component;

use Ibexa\Contracts\TwigComponents\ComponentInterface;
use Twig\Environment;

class LinkComponent implements ComponentInterface
{
    private Environment $twig;

    private string $href;

    private string $type;

    private string $rel;

    private ?string $crossorigin;

    private ?string $integrity;

    public function __construct(
        Environment $twig,
        string $href,
        string $type = 'text/css',
        string $rel = 'stylesheet',
        ?string $crossorigin = null,
        ?string $integrity = null
    ) {
        $this->twig = $twig;
        $this->href = $href;
        $this->type = $type;
        $this->rel = $rel;
        $this->crossorigin = $crossorigin;
        $this->integrity = $integrity;
    }

    /**
     * @param array<mixed> $parameters
     */
    public function render(array $parameters = []): string
    {
        return $this->twig->render(
            '@ibexadesign/twig_components/link.html.twig',
            array_merge(
                [
                    'href' => $this->href,
                    'type' => $this->type,
                    'rel' => $this->rel,
                    'crossorigin' => $this->crossorigin,
                    'integrity' => $this->integrity,
                ],
                $parameters
            )
        );
    }
}
