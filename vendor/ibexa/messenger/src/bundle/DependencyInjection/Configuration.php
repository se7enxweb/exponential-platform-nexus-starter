<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Messenger\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('ibexa_messenger');

        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->scalarNode('transport_dsn')
                    ->info('The DSN of the transport, as expected by Symfony Messenger transport factory.')
                    ->defaultValue('doctrine://ibexa.current?table_name=ibexa_messenger_messages&auto_setup=false')
                ->end()
                ->arrayNode('deduplication_lock_storage')
                    ->canBeDisabled()
                    ->children()
                        ->enumNode('type')
                            ->info('Doctrine DBAL primary connection or custom service')
                            ->values(['doctrine', 'custom', 'service'])
                            ->defaultValue('doctrine')
                        ->end()
                        ->scalarNode('service')
                            ->info('The service ID of a custom Lock Store, if "service" type is selected')
                            ->defaultNull()
                        ->end()
                        ->scalarNode('dsn')
                            ->info('The DSN of the lock store, if "custom" type is selected')
                            ->defaultNull()
                        ->end()
                    ->end()
                    ->validate()
                        ->ifTrue(static function (array $v): bool {
                            if ($v['enabled'] === false || $v['type'] !== 'service') {
                                return false;
                            }

                            return !isset($v['service']);
                        })
                        ->thenInvalid('Invalid lock store configuration. Service ID is required for "custom" type.')
                    ->end()
                    ->validate()
                        ->ifTrue(static function (array $v): bool {
                            if ($v['enabled'] === false || $v['type'] !== 'custom') {
                                return false;
                            }

                            return !isset($v['dsn']);
                        })
                        ->thenInvalid('Invalid lock store configuration. "dsn" is required for "custom" type.')
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
