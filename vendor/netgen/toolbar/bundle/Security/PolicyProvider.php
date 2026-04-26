<?php

declare(strict_types=1);

namespace Netgen\Bundle\ToolbarBundle\Security;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ConfigBuilderInterface;
use Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\PolicyProviderInterface;

final class PolicyProvider implements PolicyProviderInterface
{
    public function addPolicies(ConfigBuilderInterface $configBuilder): void
    {
        $configBuilder->addConfig(
            [
                'ngtoolbar' => [
                    'use' => null,
                ],
            ],
        );
    }
}
