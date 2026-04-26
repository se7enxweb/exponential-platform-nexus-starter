<?php

declare(strict_types=1);

namespace Netgen\Bundle\ToolbarBundle;

use Netgen\Bundle\ToolbarBundle\Security\PolicyProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class NetgenToolbarBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $ibexaCoreExtension */
        $ibexaCoreExtension = $container->getExtension('ibexa');
        $ibexaCoreExtension->addPolicyProvider(new PolicyProvider());
    }
}
