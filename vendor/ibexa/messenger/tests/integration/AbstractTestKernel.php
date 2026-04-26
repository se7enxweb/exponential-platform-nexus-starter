<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Messenger;

use DAMA\DoctrineTestBundle\DAMADoctrineTestBundle;
use Ibexa\Bundle\CorePersistence\IbexaCorePersistenceBundle;
use Ibexa\Bundle\Messenger\IbexaMessengerBundle;
use Ibexa\Contracts\Test\Core\IbexaTestKernel;
use Ibexa\Tests\Integration\Messenger\Stubs\FooMessageHandler;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

abstract class AbstractTestKernel extends IbexaTestKernel
{
    public function getSchemaFiles(): iterable
    {
        yield from parent::getSchemaFiles();

        yield from [
            $this->locateResource('@IbexaCoreBundle/Resources/config/storage/legacy/schema.yaml'),
            $this->locateResource('@IbexaMessengerBundle/Resources/config/schema.yaml'),
        ];
    }

    public function registerBundles(): iterable
    {
        yield from parent::registerBundles();

        yield new IbexaCorePersistenceBundle();
        yield new DAMADoctrineTestBundle();
        yield new IbexaMessengerBundle();
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        parent::registerContainerConfiguration($loader);

        $loader->load(__DIR__ . '/Resources/framework.yaml');
        $loader->load(__DIR__ . '/Resources/ibexa_messenger.yaml');
        $loader->load(__DIR__ . '/Resources/services.yaml');
    }

    protected static function getExposedServicesByClass(): iterable
    {
        yield from parent::getExposedServicesByClass();

        yield FooMessageHandler::class;
    }

    protected static function getExposedServicesById(): iterable
    {
        yield from parent::getExposedServicesById();

        yield 'ibexa.messenger.bus' => MessageBusInterface::class;
        yield 'ibexa.messenger.transport' => TransportInterface::class;
    }
}
