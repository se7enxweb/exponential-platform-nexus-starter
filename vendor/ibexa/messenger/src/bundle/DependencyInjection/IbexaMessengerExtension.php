<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Messenger\DependencyInjection;

use Ibexa\Bundle\Messenger\Middleware\DeduplicateMiddleware;
use Ibexa\Bundle\Messenger\Middleware\SudoMiddleware;
use Ibexa\Bundle\Messenger\Serializer\Normalizer\LockKeyNormalizer;
use Ibexa\Contracts\Messenger\Transport\MessageProviderInterface;
use LogicException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\Lock\PersistingStoreInterface;
use Symfony\Component\Lock\Serializer\LockNormalizer as SymfonyLockNormalizer;
use Symfony\Component\Lock\Store\StoreFactory;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * @phpstan-type TConfig array{
 *     transport_dsn: string,
 *     deduplication_lock_storage: TDeduplicationLockStorageConfig,
 * }
 * @phpstan-type TDeduplicationLockStorageConfig array{
 *     enabled: bool,
 *     type: string,
 *     dsn: string|null,
 *     service: string|null,
 * }
 */
final class IbexaMessengerExtension extends ConfigurableExtension implements PrependExtensionInterface
{
    /**
     * @phpstan-param TConfig&array<mixed> $mergedConfig
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(MessageProviderInterface::class)
            ->addTag('ibexa.messenger.sender_message_provider');

        $container->setParameter('ibexa.messenger.transport_dsn', $mergedConfig['transport_dsn']);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yaml');

        $this->configureLockStorage($mergedConfig['deduplication_lock_storage'], $container);
        $this->configureLockNormalizerBackport($container);
        $this->registerMessengerConfiguration($mergedConfig, $container);

        if ($this->shouldLoadTestServices($container)) {
            $loader->load('test/pages.yaml');
            $loader->load('test/components.yaml');
            $loader->load('test/contexts.yaml');
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->prependJMSTranslation($container);
    }

    private function prependJMSTranslation(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('jms_translation', [
            'configs' => [
                'ibexa_messenger' => [
                    'dirs' => [
                        __DIR__ . '/../../',
                    ],
                    'excluded_dirs' => ['Behat'],
                    'output_dir' => __DIR__ . '/../Resources/translations/',
                    'output_format' => 'xliff',
                ],
            ],
        ]);
    }

    private function shouldLoadTestServices(ContainerBuilder $container): bool
    {
        return $container->hasParameter('ibexa.behat.browser.enabled')
            && true === $container->getParameter('ibexa.behat.browser.enabled');
    }

    /**
     * @phpstan-param TConfig $mergedConfig
     */
    private function registerMessengerConfiguration(
        array $mergedConfig,
        ContainerBuilder $container
    ): void {
        $busId = 'ibexa.messenger.bus';

        $defaultMiddleware = [
            'before' => [
                [
                    'id' => 'add_bus_name_stamp_middleware',
                    'arguments' => [$busId],
                ],
                ['id' => 'reject_redelivered_message_middleware'],
                ['id' => 'dispatch_after_current_bus'],
                ['id' => 'failed_message_processing_middleware'],
            ],
            'after' => [
                ['id' => 'send_message'],
                ['id' => 'handle_message'],
            ],
        ];

        $middleware = [
            ['id' => SudoMiddleware::class],
        ];

        if ($mergedConfig['deduplication_lock_storage']['enabled'] === true) {
            $middleware[] = ['id' => DeduplicateMiddleware::class];
        }

        $middleware = array_merge(
            $defaultMiddleware['before'],
            $middleware,
            $defaultMiddleware['after'],
        );

        $debug = (bool)$container->getParameter('kernel.debug');
        if ($debug && class_exists(Stopwatch::class)) {
            array_unshift($middleware, ['id' => 'traceable', 'arguments' => [$busId]]);
        }

        $container->setParameter($busId . '.middleware', $middleware);
    }

    /**
     * @param TDeduplicationLockStorageConfig $lockStorageConfig
     */
    private function configureLockStorage(array $lockStorageConfig, ContainerBuilder $container): void
    {
        if ($lockStorageConfig['enabled'] === false) {
            $container->removeDefinition(DeduplicateMiddleware::class);
            $container->removeDefinition('ibexa.messenger.lock_factory');
            $container->removeDefinition('ibexa.messenger.lock_store.dbal');

            return;
        }

        $lockStorageType = $lockStorageConfig['type'];
        if ($lockStorageType === 'doctrine') {
            $storeDefinition = new Reference('ibexa.messenger.lock_store.dbal');
            $container->getDefinition('ibexa.messenger.lock_factory')->setArgument(0, $storeDefinition);

            return;
        }

        if ($lockStorageType === 'custom') {
            $storeDefinition = new Definition(PersistingStoreInterface::class);
            $storeDefinition->setFactory([StoreFactory::class, 'createStore']);
            $storeDefinition->setArguments([
                $lockStorageConfig['dsn'],
            ]);

            $container->getDefinition('ibexa.messenger.lock_factory')->setArgument(0, $storeDefinition);

            return;
        }

        if ($lockStorageType === 'service') {
            if (!is_string($lockStorageConfig['service'])) {
                throw new LogicException(sprintf(
                    'Expected service id string for lock storage, received "%s"',
                    get_debug_type($lockStorageConfig['service']),
                ));
            }
            $storeDefinition = new Reference($lockStorageConfig['service']);
            $container->getDefinition('ibexa.messenger.lock_factory')->setArgument(0, $storeDefinition);

            return;
        }

        throw new LogicException(sprintf(
            'Unknown lock storage type. Expected one of "%s", received "%s"',
            implode('", "', ['doctrine', 'custom', 'service']),
            $lockStorageType,
        ));
    }

    private function configureLockNormalizerBackport(ContainerBuilder $container): void
    {
        // Symfony 7.4 contains proper implementation
        if (class_exists(SymfonyLockNormalizer::class)) {
            $container->removeDefinition(LockKeyNormalizer::class);
            $definition = new Definition(SymfonyLockNormalizer::class);
            $definition->addTag('ibexa.messenger.serializer.normalizer', ['priority' => -60]);
            $container->setDefinition('ibexa.messenger.lock_normalizer', $definition);
        }
    }
}
