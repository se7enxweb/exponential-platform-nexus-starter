<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\TwigComponents\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(IbexaTwigComponentsExtension::EXTENSION_NAME);
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->useAttributeAsKey('group')
            ->normalizeKeys(false)
            ->arrayPrototype()
                ->useAttributeAsKey('name')
                ->normalizeKeys(false)
                ->arrayPrototype()
                    ->children()
                        ->scalarNode('type')
                            ->isRequired()
                            ->validate()
                                ->ifNotInArray(array_keys(IbexaTwigComponentsExtension::COMPONENT_MAP))
                                ->thenInvalid(sprintf(
                                    'Invalid type "%%s". Allowed types: %s',
                                    implode(', ', array_keys(IbexaTwigComponentsExtension::COMPONENT_MAP))
                                ))
                            ->end()
                        ->end()
                        ->scalarNode('priority')
                            ->defaultValue(0)
                        ->end()
                        ->arrayNode('arguments')
                        ->isRequired()
                            ->normalizeKeys(false)
                            ->variablePrototype()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
