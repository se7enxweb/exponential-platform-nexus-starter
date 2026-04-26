<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\TwigComponents\DependencyInjection;

use Ibexa\Bundle\TwigComponents\DependencyInjection\IbexaTwigComponentsExtension;
use Ibexa\Tests\Bundle\TwigComponents\Fixtures\DummyComponent;
use Ibexa\TwigComponents\Component\TemplateComponent;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

final class IbexaTwigComponentsExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [new IbexaTwigComponentsExtension()];
    }

    public function testRegistersTwigComponent(): void
    {
        $this->load([
            'test_group' => [
                'test_component' => [
                    'type' => 'template',
                    'priority' => 100,
                    'arguments' => [
                        'template' => '@ibexa/template.html.twig',
                        'parameters' => ['title' => 'Test Title'],
                    ],
                ],
            ],
        ]);

        self::assertContainerBuilderHasService('test_component', TemplateComponent::class);
        self::assertContainerBuilderHasServiceDefinitionWithTag(
            'test_component',
            'ibexa.twig.component',
            [
                'group' => 'test_group',
                'priority' => 100,
            ]
        );

        self::assertContainerBuilderHasServiceDefinitionWithArgument(
            'test_component',
            '$template',
            '@ibexa/template.html.twig'
        );
        self::assertContainerBuilderHasServiceDefinitionWithArgument(
            'test_component',
            '$parameters',
            ['title' => 'Test Title']
        );
    }

    public function testInvalidComponentTypeThrowsException(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage(
            'Unrecognized option "invalid_component" under "ibexa_twig_components.ibexa_twig_components.invalid_group'
        );

        $this->load([
            'ibexa_twig_components' => [
                'invalid_group' => [
                    'invalid_component' => [
                        'type' => 'invalid_type',
                        'arguments' => [],
                    ],
                ],
            ],
        ]);
    }

    public function testAttributeCausesTagToBeAdded(): void
    {
        $this->container
            ->register(DummyComponent::class, DummyComponent::class)
            ->setAutowired(true)
            ->setAutoconfigured(true);
        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            DummyComponent::class,
            'ibexa.twig.component',
            [
                'group' => 'test_group',
                'priority' => 100,
            ]
        );
    }
}
