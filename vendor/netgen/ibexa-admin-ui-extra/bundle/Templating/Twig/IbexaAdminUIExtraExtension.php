<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaAdminUIExtraBundle\Templating\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class IbexaAdminUIExtraExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ng_count_content_by_content_type',
                [IbexaAdminUIExtraRuntime::class, 'countContentByContentType'],
            ),
        ];
    }
}
