<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaScheduledVisibilityBundle\DependencyInjection\Compiler;

use LogicException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\Registry;

use function sprintf;

final class ScheduledVisibilityHandlerRegistrationPass implements CompilerPassInterface
{
    private string $handlerRegistryId = Registry::class;
    private string $handlerTag = 'netgen.ibexa_scheduled_visibility.handler';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has($this->handlerRegistryId)) {
            return;
        }

        $handlerRegistryDefinition = $container->getDefinition($this->handlerRegistryId);

        $handlers = $container->findTaggedServiceIds($this->handlerTag);

        foreach ($handlers as $id => $attributes) {
            $this->registerHandler($handlerRegistryDefinition, $id, $attributes);
        }
    }

    /**
     * @throws LogicException
     */
    private function registerHandler(Definition $handlerRegistryDefinition, string $id, array $attributes): void
    {
        foreach ($attributes as $attribute) {
            if (!isset($attribute['identifier'])) {
                throw new LogicException(
                    sprintf(
                        "'%s' service tag needs an 'identifier' attribute to identify the handler",
                        $this->handlerTag,
                    ),
                );
            }

            $handlerRegistryDefinition->addMethodCall(
                'register',
                [
                    $attribute['identifier'],
                    new Reference($id),
                ],
            );
        }
    }
}
