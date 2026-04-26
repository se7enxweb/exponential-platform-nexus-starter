<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaAdminUIExtraBundle\DependencyInjection\Compiler;

use Composer\InstalledVersions;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class DisableQueuesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!InstalledVersions::isInstalled('symfony/messenger')) {
            $container->setParameter(
                'netgen_ibexa_admin_ui_extra.queues.enabled',
                false,
            );
        }
    }
}
