<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsIbexaSiteApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

final class Configuration implements ConfigurationInterface
{
    public function __construct(
        private ExtensionInterface $extension,
    ) {}

    /**
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder<'array'>
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder($this->extension->getAlias());
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->enumNode('search_service_adapter')
                    ->defaultValue(null)
                    ->values([null, 'filter', 'find'])
                ->end()
            ->end();

        return $treeBuilder;
    }
}
