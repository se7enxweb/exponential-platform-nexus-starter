<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaScheduledVisibilityBundle;

use Netgen\Bundle\IbexaScheduledVisibilityBundle\DependencyInjection\Compiler\ScheduledVisibilityHandlerRegistrationPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class NetgenIbexaScheduledVisibilityBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new ScheduledVisibilityHandlerRegistrationPass());
    }
}
