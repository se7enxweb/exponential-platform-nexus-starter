<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserIbexaBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

use function array_key_exists;
use function file_get_contents;

final class NetgenContentBrowserIbexaExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config'),
        );

        $loader->load('ibexa/services.yaml');

        /** @var array<string, string> $activatedBundles */
        $activatedBundles = $container->getParameter('kernel.bundles');

        if (array_key_exists('NetgenTagsBundle', $activatedBundles)) {
            $loader->load('netgen_tags/services.yaml');
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config'),
        );

        $loader->load('default_settings.yaml');

        $prependConfigs = [
            'ibexa/image.yaml' => 'ibexa',
            'ibexa/item_types.yaml' => 'netgen_content_browser',
        ];

        /** @var array<string, string> $activatedBundles */
        $activatedBundles = $container->getParameter('kernel.bundles');

        if (array_key_exists('NetgenTagsBundle', $activatedBundles)) {
            $prependConfigs['netgen_tags/item_types.yaml'] = 'netgen_content_browser';
        }

        foreach ($prependConfigs as $configFile => $prependConfig) {
            $configFile = __DIR__ . '/../Resources/config/' . $configFile;
            $config = Yaml::parse((string) file_get_contents($configFile));
            $container->prependExtensionConfig($prependConfig, $config);
            $container->addResource(new FileResource($configFile));
        }
    }
}
