<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Messenger\DependencyInjection\CompilerPass;

use Ibexa\Bundle\Messenger\Transport\ConnectionRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class TransportFactoryCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('messenger.transport.doctrine.factory')) {
            return;
        }

        $doctrineFactoryDefinition = $container->getDefinition('messenger.transport.doctrine.factory');
        $doctrineFactoryDefinition->replaceArgument(
            0,
            new Reference(ConnectionRegistry::class),
        );
    }
}
