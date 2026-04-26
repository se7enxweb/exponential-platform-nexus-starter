<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Tests\Integration\SetupFactory;

use Ibexa\Contracts\Core\Test\Repository\SetupFactory\Legacy as CoreLegacySetupFactory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Legacy extends CoreLegacySetupFactory
{
    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    protected function externalBuildContainer(ContainerBuilder $containerBuilder): void
    {
        $loader = new YamlFileLoader(
            $containerBuilder,
            new FileLocator(__DIR__ . '/../../../../lib/Resources/config/'),
        );

        $loader->load('search/legacy.yaml');
    }
}
