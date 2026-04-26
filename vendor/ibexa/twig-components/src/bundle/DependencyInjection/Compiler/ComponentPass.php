<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\TwigComponents\DependencyInjection\Compiler;

use Ibexa\Contracts\TwigComponents\Exception\InvalidArgumentException;
use Ibexa\TwigComponents\Component\Registry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ComponentPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public const string TAG_NAME = 'ibexa.twig.component';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(Registry::class)) {
            return;
        }

        $registryDefinition = $container->getDefinition(Registry::class);
        $services = $this->findAndSortTaggedServices(self::TAG_NAME, $container);

        foreach ($services as $serviceReference) {
            $id = (string)$serviceReference;
            $definition = $container->getDefinition($id);
            $tags = $definition->getTag(self::TAG_NAME);

            foreach ($tags as $tag) {
                if (!isset($tag['group'])) {
                    throw new InvalidArgumentException(
                        $id,
                        'Tag ' . self::TAG_NAME . ' must contain a "group" argument.',
                    );
                }
                $id = $tag['id'] ?? $id;
                $registryDefinition->addMethodCall(
                    'addComponent',
                    [$tag['group'], $id, $serviceReference]
                );
            }
        }
    }
}
