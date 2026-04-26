<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaFormsBundle;

use Netgen\Bundle\IbexaFormsBundle\DependencyInjection\Compiler\FieldTypeHandlerRegistryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class NetgenIbexaFormsBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new FieldTypeHandlerRegistryPass());
    }
}
