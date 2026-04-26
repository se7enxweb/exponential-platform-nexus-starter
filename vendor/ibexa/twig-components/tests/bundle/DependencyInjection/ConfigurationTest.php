<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\FieldTypeRichText\DependencyInjection;

use Ibexa\Bundle\TwigComponents\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;

final class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }

    /**
     * @return array<string, mixed>
     */
    public function providerForTestProcessingConfiguration(): array
    {
        return [
            'basic configuration' => [
                [
                    'ibexa_twig_components' => [
                        'foo_group1' => [
                            'foo_component1' => [
                                'type' => 'script',
                                'priority' => 100,
                                'arguments' => [
                                    'src' => 'script.js',
                                ],
                            ],
                            'foo_component2' => [
                                'type' => 'template',
                                'arguments' => [
                                    'template' => 'template.html.twig',
                                ],
                            ],
                            'foo_component3' => [
                                'type' => 'html',
                                'arguments' => [
                                    'content' => 'template.html.twig',
                                ],
                            ],
                        ],
                        'foo_group2' => [
                            'foo_component1' => [
                                'type' => 'controller',
                                'arguments' => [
                                    'controler' => 'SomeController:SomeAction',
                                ],
                            ],
                            'foo_component2' => [
                                'type' => 'stylesheet',
                                'arguments' => [
                                    'content' => 'stylesheet.css',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'foo_group1' => [
                        'foo_component1' => [
                            'type' => 'script',
                            'priority' => 100,
                            'arguments' => [
                                'src' => 'script.js',
                            ],
                        ],
                        'foo_component2' => [
                            'type' => 'template',
                            'priority' => 0,
                            'arguments' => [
                                'template' => 'template.html.twig',
                            ],
                        ],
                        'foo_component3' => [
                            'type' => 'html',
                            'priority' => 0,
                            'arguments' => [
                                'content' => 'template.html.twig',
                            ],
                        ],
                    ],
                    'foo_group2' => [
                        'foo_component1' => [
                            'type' => 'controller',
                            'priority' => 0,
                            'arguments' => [
                                'controler' => 'SomeController:SomeAction',
                            ],
                        ],
                        'foo_component2' => [
                            'type' => 'stylesheet',
                            'priority' => 0,
                            'arguments' => [
                                'content' => 'stylesheet.css',
                            ],
                        ],
                    ],
                ],
            ],
            'configuration with parameters' => [
                [
                    'ibexa_twig_components' => [
                        'foo_group1' => [
                            'foo_component1' => [
                                'type' => 'template',
                                'arguments' => [
                                    'template' => 'template.html.twig',
                                    'parameters' => [
                                        'parameter1' => 'value1',
                                        'parameter2' => 'value2',
                                    ],
                                ],
                            ],
                            'foo_component2' => [
                                'type' => 'controller',
                                'arguments' => [
                                    'controler' => 'SomeController:SomeAction',
                                    'parameters' => [
                                        'parameter1' => 'value1',
                                        'parameter2' => 'value2',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'foo_group1' => [
                        'foo_component1' => [
                            'type' => 'template',
                            'priority' => 0,
                            'arguments' => [
                                'template' => 'template.html.twig',
                                'parameters' => [
                                    'parameter1' => 'value1',
                                    'parameter2' => 'value2',
                                ],
                            ],
                        ],
                        'foo_component2' => [
                            'type' => 'controller',
                            'priority' => 0,
                            'arguments' => [
                                'controler' => 'SomeController:SomeAction',
                                'parameters' => [
                                    'parameter1' => 'value1',
                                    'parameter2' => 'value2',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'empty configuration' => [
                [
                    'ibexa_twig_components' => [
                    ],
                ],
                [],
            ],
        ];
    }

    /**
     * @dataProvider providerForTestProcessingConfiguration
     *
     * @param array<string, mixed> $configurationValues
     * @param array<string, mixed> $expectedProcessedConfiguration
     */
    public function testProcessingConfiguration(
        array $configurationValues,
        array $expectedProcessedConfiguration
    ): void {
        $this->assertProcessedConfigurationEquals($configurationValues, $expectedProcessedConfiguration);
    }

    /**
     * @return array<string, mixed>
     */
    public function providerForTestProcessingInvalidConfiguration(): array
    {
        return [
            'invalid type' => [
                [
                    'ibexa_twig_components' => [
                        'foo_group' => [
                            'foo_component' => [
                                'type' => 'invalid_type',
                                'arguments' => [
                                    'src' => 'script.js',
                                ],
                            ],
                        ],
                    ],
                ],
                'Invalid configuration for path "ibexa_twig_components.foo_group.foo_component.type": Invalid type ""invalid_type"". Allowed types: script, stylesheet, template, controller, html',
            ],
            'missing type' => [
                [
                    'ibexa_twig_components' => [
                        'foo_group' => [
                            'foo_component' => [
                                'arguments' => [
                                    'src' => 'script.js',
                                ],
                            ],
                        ],
                    ],
                ],
                'The child config "type" under "ibexa_twig_components.foo_group.foo_component" must be configured.',
            ],
            'no arguments' => [
                [
                    'ibexa_twig_components' => [
                        'foo_group' => [
                            'foo_component' => [
                                'type' => 'script',
                            ],
                        ],
                    ],
                ],
                'The child config "arguments" under "ibexa_twig_components.foo_group.foo_component" must be configured.',
            ],
        ];
    }

    /**
     * @dataProvider providerForTestProcessingInvalidConfiguration
     *
     * @param array<string, mixed> $configurationValues
     */
    public function testProcessingInvalidConfiguration(array $configurationValues, string $expectedMessage): void
    {
        $this->assertConfigurationIsInvalid(
            $configurationValues,
            $expectedMessage
        );
    }
}
