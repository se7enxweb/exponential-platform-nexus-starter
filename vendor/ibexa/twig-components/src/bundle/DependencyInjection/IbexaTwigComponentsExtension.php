<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\TwigComponents\DependencyInjection;

use Ibexa\Bundle\TwigComponents\DependencyInjection\Compiler\ComponentPass;
use Ibexa\Contracts\TwigComponents\Attribute\AsTwigComponent;
use Ibexa\Contracts\TwigComponents\Exception\InvalidArgumentException;
use Ibexa\TwigComponents\Component\ControllerComponent;
use Ibexa\TwigComponents\Component\HtmlComponent;
use Ibexa\TwigComponents\Component\LinkComponent;
use Ibexa\TwigComponents\Component\MenuComponent;
use Ibexa\TwigComponents\Component\ScriptComponent;
use Ibexa\TwigComponents\Component\TemplateComponent;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

final class IbexaTwigComponentsExtension extends Extension implements PrependExtensionInterface
{
    public const COMPONENT_MAP = [
        'script' => ScriptComponent::class,
        'stylesheet' => LinkComponent::class,
        'template' => TemplateComponent::class,
        'controller' => ControllerComponent::class,
        'html' => HtmlComponent::class,
        'menu' => MenuComponent::class,
    ];

    public const EXTENSION_NAME = 'ibexa_twig_components';

    /**
     * @param array<string, mixed> $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yaml');

        if ($this->shouldLoadTestServices($container)) {
            $loader->load('test/pages.yaml');
            $loader->load('test/components.yaml');
            $loader->load('test/contexts.yaml');
        }

        $configuration = $this->processConfiguration(new Configuration(), $configs);
        $this->registerConfiguredComponents($configuration, $container);

        $container->registerAttributeForAutoconfiguration(
            AsTwigComponent::class,
            static function (Definition $definition, AsTwigComponent $attribute): void {
                $definition->addTag('ibexa.twig.component', [
                    'group' => $attribute->group,
                    'priority' => $attribute->priority,
                ]);
            }
        );
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->prependDefaultConfiguration($container);
        $this->prependJMSTranslation($container);
    }

    private function prependDefaultConfiguration(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/prepend.yaml';

        $container->addResource(new FileResource($configFile));

        $configs = Yaml::parseFile($configFile, Yaml::PARSE_CONSTANT) ?? [];
        foreach ($configs as $name => $config) {
            $container->prependExtensionConfig($name, $config);
        }
    }

    private function prependJMSTranslation(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('jms_translation', [
            'configs' => [
                self::EXTENSION_NAME => [
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
     * @param array<string, mixed> $config
     */
    private function registerConfiguredComponents(array $config, ContainerBuilder $container): void
    {
        foreach ($config as $group => $components) {
            foreach ($components as $name => $componentConfig) {
                $type = $componentConfig['type'] ?? null;
                if (!isset(self::COMPONENT_MAP[$type])) {
                    throw new InvalidArgumentException(
                        $type,
                        sprintf('Invalid component type "%s" for component "%s"', $type, $name)
                    );
                }
                $className = self::COMPONENT_MAP[$type];

                $arguments = $componentConfig['arguments'];
                $modifiedArguments = [];
                foreach ($arguments as $key => $value) {
                    $modifiedArguments['$' . $key] = $value;
                }

                $definition = new Definition($className);
                $definition->setArguments($modifiedArguments);
                $definition->setLazy(true);
                $definition->setAutowired(true);
                $definition->setAutoconfigured(true);
                $definition->setPublic(false);
                $definition->addTag(
                    ComponentPass::TAG_NAME,
                    [
                        'group' => $group,
                        'priority' => $componentConfig['priority'],
                    ]
                );

                $container->setDefinition($name, $definition);
            }
        }
    }
}
