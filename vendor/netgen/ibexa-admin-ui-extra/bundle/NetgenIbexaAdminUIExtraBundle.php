<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaAdminUIExtraBundle;

use Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension;
use Netgen\Bundle\IbexaAdminUIExtraBundle\DependencyInjection\Compiler\DisableQueuesPass;
use Netgen\Bundle\IbexaAdminUIExtraBundle\DependencyInjection\Compiler\SearchOverridePass;
use Netgen\Bundle\IbexaAdminUIExtraBundle\DependencyInjection\Security\PolicyProvider\QueuesPolicyProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class NetgenIbexaAdminUIExtraBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new SearchOverridePass());
        $container->addCompilerPass(new DisableQueuesPass());

        /** @var IbexaCoreExtension $ibexaCoreExtension */
        $ibexaCoreExtension = $container->getExtension('ibexa');
        $ibexaCoreExtension->addPolicyProvider(new QueuesPolicyProvider());
    }
}
