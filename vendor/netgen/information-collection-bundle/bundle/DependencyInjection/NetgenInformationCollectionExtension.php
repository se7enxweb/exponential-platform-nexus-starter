<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\DependencyInjection;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Ibexa\Core\Helper\TranslationHelper;
use League\Csv\Writer;
use Netgen\InformationCollection\API\Action\ActionInterface;
use Netgen\InformationCollection\API\ConfigurationConstants;
use Netgen\InformationCollection\Core\Export\CsvExportResponseFormatter;
use Netgen\InformationCollection\Core\Export\XlsExportResponseFormatter;
use Netgen\InformationCollection\Core\Export\XlsxExportResponseFormatter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Yaml\Yaml;

use function class_exists;
use function file_get_contents;

class NetgenInformationCollectionExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $bundleResourceLoader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $bundleResourceLoader->load('services.yml');

        $libResourceLoader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../../lib/Resources/config'));
        $libResourceLoader->load('services.yml');
        $libResourceLoader->load('parameters.yml');
        $libResourceLoader->load('default_settings.yml');

        $this->processSemanticConfig($container, $config);

        $this->setUpAutoConfiguration($container);
        $this->registerServiceDefinitions($container);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->addTwigConfig($container);
        $this->addDoctrineConfig($container);
    }

    protected function addDoctrineConfig(ContainerBuilder $container): void
    {
        $configDir = __DIR__ . '/../../lib/Doctrine/mappings';

        $config = [
            'orm' => [
                'auto_mapping' => true,
                'mappings' => [
                    'NetgenInformationCollectionBundle' => [
                        'is_bundle' => false,
                        'dir' => $configDir,
                        'type' => 'xml',
                        'prefix' => 'Netgen\InformationCollection\Doctrine\Entity',
                    ],
                ],
            ],
        ];

        $container->prependExtensionConfig('doctrine', $config);
    }

    protected function addTwigConfig(ContainerBuilder $container): void
    {
        $configs = [
            'twig.yml' => 'twig',
        ];

        foreach ($configs as $fileName => $extensionName) {
            $configFile = __DIR__ . '/../../lib/Resources/config/' . $fileName;
            $config = Yaml::parse((string) file_get_contents($configFile));
            $container->prependExtensionConfig($extensionName, $config);
            $container->addResource(new FileResource($configFile));
        }
    }

    protected function setUpAutoConfiguration(ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(ActionInterface::class)
            ->addTag('netgen_information_collection.action');
    }

    protected function registerServiceDefinitions(ContainerBuilder $container): void
    {
        $definitions = [];

        if (class_exists(Writer::class)) {
            $csvExportFormatter = new Definition(
                CsvExportResponseFormatter::class,
            );
            $csvExportFormatter->addTag('netgen_information_collection.export.formatter');
            $csvExportFormatter->addArgument(new Reference(TranslationHelper::class));
            $csvExportFormatter->addArgument(new Reference('ibexa.config.resolver'));
            $csvExportFormatter->addArgument(new Reference(SluggerInterface::class));
            $csvExportFormatter->setPublic(false);
            $csvExportFormatter->setAutowired(false);
            $csvExportFormatter->setAutoconfigured(false);

            $definitions[] = $csvExportFormatter;
        }

        if (class_exists(Spreadsheet::class)) {
            $xlsExportFormatter = new Definition(
                XlsExportResponseFormatter::class,
            );
            $xlsExportFormatter->addTag('netgen_information_collection.export.formatter');
            $xlsExportFormatter->addArgument(new Reference(TranslationHelper::class));
            $xlsExportFormatter->addArgument(new Reference(SluggerInterface::class));
            $xlsExportFormatter->setPublic(false);
            $xlsExportFormatter->setAutowired(false);
            $xlsExportFormatter->setAutoconfigured(false);

            $xlsxExportFormatter = new Definition(
                XlsxExportResponseFormatter::class,
            );
            $xlsxExportFormatter->addTag('netgen_information_collection.export.formatter');
            $xlsxExportFormatter->addArgument(new Reference(TranslationHelper::class));
            $xlsxExportFormatter->addArgument(new Reference(SluggerInterface::class));
            $xlsxExportFormatter->setPublic(false);
            $xlsxExportFormatter->setAutowired(false);
            $xlsxExportFormatter->setAutoconfigured(false);

            $definitions[$xlsExportFormatter->getClass()] = $xlsExportFormatter;
            $definitions[$xlsxExportFormatter->getClass()] = $xlsxExportFormatter;
        }

        if (!empty($definitions)) {
            $container->addDefinitions($definitions);
        }
    }

    private function processSemanticConfig(ContainerBuilder $container, array $config): void
    {
        $processor = new ConfigurationProcessor($container, ConfigurationConstants::SETTINGS_ROOT);
        $processor->mapConfig(
            $config,
            static function ($config, $scope, ContextualizerInterface $c): void {
                $c->setContextualParameter('actions', $scope, $config['actions']);
                $c->setContextualParameter('action_config', $scope, $config['action_config']);
                $c->setContextualParameter('captcha', $scope, $config['captcha']);
                $c->setContextualParameter('export', $scope, $config['export']);
            },
        );
    }
}
