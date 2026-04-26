<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Messenger\DependencyInjection;

use Ibexa\Bundle\Messenger\DependencyInjection\IbexaMessengerExtension;
use Ibexa\Bundle\Messenger\Middleware\DeduplicateMiddleware;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

final class IbexaMessengerExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container->setParameter('kernel.bundles', []);
        $this->container->setParameter('kernel.debug', false);
    }

    protected function getContainerExtensions(): array
    {
        return [
            new IbexaMessengerExtension(),
        ];
    }

    public function testConfigurationDefaults(): void
    {
        $this->load();

        self::assertTrue($this->container->hasDefinition(DeduplicateMiddleware::class));
        self::assertTrue($this->container->hasDefinition('ibexa.messenger.lock_factory'));
        self::assertTrue($this->container->hasDefinition('ibexa.messenger.lock_store.dbal'));
    }

    public function testConfigurationWithDisabledLocks(): void
    {
        $this->load([
            'deduplication_lock_storage' => [
                'enabled' => false,
            ],
        ]);

        self::assertFalse($this->container->hasDefinition(DeduplicateMiddleware::class));
        self::assertFalse($this->container->hasDefinition('ibexa.messenger.lock_factory'));
        self::assertFalse($this->container->hasDefinition('ibexa.messenger.lock_store.dbal'));
    }

    public function testConfigurationWithCustomTransport(): void
    {
        $this->load([
            'transport_dsn' => 'redis://redis.messenger_default:6379/ibexa_messages?delete_after_ack=0&amp;read_timeout=5',
        ]);

        $definition = $this->container->getDefinition('ibexa.messenger.transport');
        self::assertSame('%ibexa.messenger.transport_dsn%', $definition->getArgument(0));

        self::assertSame(
            'redis://redis.messenger_default:6379/ibexa_messages?delete_after_ack=0&amp;read_timeout=5',
            $this->container->getParameter('ibexa.messenger.transport_dsn'),
        );
    }
}
