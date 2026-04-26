<?php

declare(strict_types=1);

namespace Netgen\Bundle\ToolbarBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('netgen_toolbar');

        /** @var \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('default_admin_site_access')
                    ->cannotBeEmpty()
                    ->defaultValue('%ngsite.admin_siteaccess_name%')
                ->end()
                ->scalarNode('legacy_admin_site_access')
                    ->cannotBeEmpty()
                    ->defaultValue('ngadminui')
                ->end()
                ->arrayNode('admin_site_access_mapping')
                    ->cannotBeEmpty()
                    ->prototype('scalar')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
