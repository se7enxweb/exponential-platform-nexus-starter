<?php

declare(strict_types=1);

namespace Netgen\IbexaScheduledVisibility\Tests\Integration\SetupFactory;

use Ibexa\Contracts\Core\Test\Repository\SetupFactory\Legacy as CoreLegacySetupFactory;
use Netgen\Bundle\IbexaScheduledVisibilityBundle\DependencyInjection\Compiler\ScheduledVisibilityHandlerRegistrationPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class Legacy extends CoreLegacySetupFactory
{
    protected function externalBuildContainer(ContainerBuilder $containerBuilder): void
    {
        $loader = new YamlFileLoader(
            $containerBuilder,
            new FileLocator(__DIR__ . '/../../../../bundle/Resources/config/'),
        );

        $loader->load('services.yaml');
        $loader->load('default_settings.yaml');

        $containerBuilder->addCompilerPass(new ScheduledVisibilityHandlerRegistrationPass());
    }
}
