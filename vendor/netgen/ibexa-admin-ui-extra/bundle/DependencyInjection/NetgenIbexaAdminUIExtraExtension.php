<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaAdminUIExtraBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\GlobFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

use function file_get_contents;

final class NetgenIbexaAdminUIExtraExtension extends Extension implements PrependExtensionInterface
{
    public function getAlias(): string
    {
        return 'netgen_ibexa_admin_ui_extra';
    }

    public function getConfiguration(array $config, ContainerBuilder $container): Configuration
    {
        return new Configuration($this->getAlias());
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $locator = new FileLocator(__DIR__ . '/../Resources/config');

        $loader = new DelegatingLoader(
            new LoaderResolver(
                [
                    new GlobFileLoader($container, $locator),
                    new YamlFileLoader($container, $locator),
                ],
            ),
        );

        $loader->load('services/*.yaml', 'glob');
        $loader->load('default_settings.yaml');

        $this->processExtensionConfiguration($configs, $container);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $configs = [
            'design.yaml' => 'ibexa_design_engine',
        ];

        foreach ($configs as $fileName => $extensionName) {
            $configFile = __DIR__ . '/../Resources/config/' . $fileName;
            $config = Yaml::parse((string) file_get_contents($configFile));
            $container->prependExtensionConfig($extensionName, $config);
            $container->addResource(new FileResource($configFile));
        }
    }

    private function processExtensionConfiguration(array $configs, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration($configs, $container);
        $configuration = $this->processConfiguration($configuration, $configs);

        $this->processShowSiteaccessOutsideConfiguredContentTreeRootConfiguration($configuration, $container);
        $this->processQueuesConfiguration($configuration, $container);
    }

    private function processShowSiteaccessOutsideConfiguredContentTreeRootConfiguration(
        array $configuration,
        ContainerBuilder $container,
    ): void {
        $container->setParameter(
            'netgen_ibexa_admin_ui_extra.show_siteaccess_urls_outside_configured_content_tree_root',
            $configuration['show_siteaccess_urls_outside_configured_content_tree_root'],
        );
    }

    private function processQueuesConfiguration(array $configuration, ContainerBuilder $container): void
    {
        $container->setParameter(
            'netgen_ibexa_admin_ui_extra.queues.enabled',
            $configuration['queues']['enabled'],
        );

        $container->setParameter(
            'netgen_ibexa_admin_ui_extra.queues.transports',
            $configuration['queues']['transports'],
        );
    }
}
