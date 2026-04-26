<?php

declare(strict_types=1);

namespace Netgen\IbexaScheduledVisibility\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Netgen\Bundle\IbexaScheduledVisibilityBundle\DependencyInjection\NetgenIbexaScheduledVisibilityExtension;
use PHPUnit\Framework\Attributes\DataProvider;

final class NetgenIbexaScheduledVisibilityTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setParameter('kernel.bundles', []);
    }

    public static function provideDefaultConfigurationCases(): iterable
    {
        return [
            [
                [],
            ],
            [
                [
                    'enabled' => false,
                ],
            ],
            [
                [
                    'enabled' => false,
                    'handler' => 'content',
                ],
            ],
            [
                [
                    'enabled' => false,
                    'handler' => 'content',
                    'sections' => [
                        'visible' => [
                            'section_id' => 0,
                        ],
                        'hidden' => [
                            'section_id' => 0,
                        ],
                    ],
                ],
            ],
            [
                [
                    'enabled' => false,
                    'handler' => 'content',
                    'sections' => [
                        'visible' => [
                            'section_id' => 0,
                        ],
                        'hidden' => [
                            'section_id' => 0,
                        ],
                    ],
                    'object_states' => [
                        'object_state_group_id' => 0,
                        'visible' => [
                            'object_state_id' => 0,
                        ],
                        'hidden' => [
                            'object_state_id' => 0,
                        ],
                    ],
                ],
            ],
            [
                [
                    'enabled' => false,
                    'handler' => 'content',
                    'sections' => [
                        'visible' => [
                            'section_id' => 0,
                        ],
                        'hidden' => [
                            'section_id' => 0,
                        ],
                    ],
                    'object_states' => [
                        'object_state_group_id' => 0,
                        'visible' => [
                            'object_state_id' => 0,
                        ],
                        'hidden' => [
                            'object_state_id' => 0,
                        ],
                    ],
                    'content_types' => [
                        'all' => false,
                        'allowed' => [],
                    ],
                ],
            ],
        ];
    }

    public static function provideEnabledConfigurationCases(): iterable
    {
        return [
            [
                [],
                false,
            ],
            [
                [
                    'enabled' => false,
                ],
                false,
            ],
            [
                [
                    'enabled' => true,
                ],
                true,
            ],
        ];
    }

    public static function provideHandlerConfigurationCases(): iterable
    {
        return [
            [
                [
                    'handler' => 'content',
                ],
                'content',
            ],
            [
                [
                    'handler' => 'location',
                ],
                'location',
            ],
            [
                [
                    'handler' => 'content_location',
                ],
                'content_location',
            ],
            [
                [
                    'handler' => 'section',
                ],
                'section',
            ],
            [
                [
                    'handler' => 'object_state',
                ],
                'object_state',
            ],
        ];
    }

    public static function provideSectionConfigurationCases(): iterable
    {
        return [
            [
                [
                    'sections' => [
                        'visible' => [
                            'section_id' => 1,
                        ],
                        'hidden' => [
                            'section_id' => 2,
                        ],
                    ],
                ],
                [
                    'visible' => 1,
                    'hidden' => 2,
                ],
            ],
            [
                [
                    'sections' => [
                        'visible' => [
                            'section_id' => 2,
                        ],
                        'hidden' => [
                            'section_id' => 1,
                        ],
                    ],
                ],
                [
                    'visible' => 2,
                    'hidden' => 1,
                ],
            ],
        ];
    }

    public static function provideObjectStateConfigurationCases(): iterable
    {
        return [
            [
                [
                    'object_states' => [
                        'object_state_group_id' => 1,
                        'visible' => [
                            'object_state_id' => 2,
                        ],
                        'hidden' => [
                            'object_state_id' => 3,
                        ],
                    ],
                ],
                [
                    'group' => 1,
                    'visible' => 2,
                    'hidden' => 3,
                ],
            ],
            [
                [
                    'object_states' => [
                        'object_state_group_id' => 3,
                        'visible' => [
                            'object_state_id' => 2,
                        ],
                        'hidden' => [
                            'object_state_id' => 1,
                        ],
                    ],
                ],
                [
                    'group' => 3,
                    'visible' => 2,
                    'hidden' => 1,
                ],
            ],
        ];
    }

    public static function provideContentTypesConfigurationCases(): iterable
    {
        return [
            [
                [
                    'content_types' => [],
                ],
                [
                    'all' => false,
                    'allowed' => [],
                ],
            ],
            [
                [
                    'content_types' => [
                        'all' => true,
                        'allowed' => [],
                    ],
                ],
                [
                    'all' => true,
                    'allowed' => [],
                ],
            ],
            [
                [
                    'content_types' => [
                        'all' => false,
                        'allowed' => ['content_1', 'content_2'],
                    ],
                ],
                [
                    'all' => false,
                    'allowed' => ['content_1', 'content_2'],
                ],
            ],
        ];
    }

    #[DataProvider('provideDefaultConfigurationCases')]
    public function testDefaultConfiguration(array $configuration): void
    {
        $this->load($configuration);

        $this->assertContainerBuilderHasParameter(
            'netgen_ibexa_scheduled_visibility.enabled',
            false,
        );
        $this->assertContainerBuilderHasParameter(
            'netgen_ibexa_scheduled_visibility.handler',
            'content',
        );
        $this->assertContainerBuilderHasParameter(
            'netgen_ibexa_scheduled_visibility.sections.visible.section_id',
            0,
        );
        $this->assertContainerBuilderHasParameter(
            'netgen_ibexa_scheduled_visibility.sections.hidden.section_id',
            0,
        );
        $this->assertContainerBuilderHasParameter(
            'netgen_ibexa_scheduled_visibility.object_states.object_state_group_id',
            0,
        );
        $this->assertContainerBuilderHasParameter(
            'netgen_ibexa_scheduled_visibility.object_states.visible.object_state_id',
            0,
        );
        $this->assertContainerBuilderHasParameter(
            'netgen_ibexa_scheduled_visibility.object_states.hidden.object_state_id',
            0,
        );
        $this->assertContainerBuilderHasParameter(
            'netgen_ibexa_scheduled_visibility.content_types.all',
            false,
        );
        $this->assertContainerBuilderHasParameter(
            'netgen_ibexa_scheduled_visibility.content_types.allowed',
            [],
        );
    }

    #[DataProvider('provideEnabledConfigurationCases')]
    public function testEnabledConfiguration(array $configuration, bool $expectedParameterValue): void
    {
        $this->load($configuration);

        $this->assertContainerBuilderHasParameter(
            'netgen_ibexa_scheduled_visibility.enabled',
            $expectedParameterValue,
        );
    }

    #[DataProvider('provideHandlerConfigurationCases')]
    public function testHandlerConfiguration(array $configuration, string $expectedParameterValue): void
    {
        $this->load($configuration);

        $this->assertContainerBuilderHasParameter(
            'netgen_ibexa_scheduled_visibility.handler',
            $expectedParameterValue,
        );
    }

    #[DataProvider('provideSectionConfigurationCases')]
    public function testSectionConfiguration(array $configuration, array $expectedParameterValues): void
    {
        $this->load($configuration);

        $this->assertContainerBuilderHasParameter(
            'netgen_ibexa_scheduled_visibility.sections.visible.section_id',
            $expectedParameterValues['visible'],
        );
        $this->assertContainerBuilderHasParameter(
            'netgen_ibexa_scheduled_visibility.sections.hidden.section_id',
            $expectedParameterValues['hidden'],
        );
    }

    #[DataProvider('provideObjectStateConfigurationCases')]
    public function testObjectStateConfiguration(array $configuration, array $expectedParameterValues): void
    {
        $this->load($configuration);

        $this->assertContainerBuilderHasParameter(
            'netgen_ibexa_scheduled_visibility.object_states.object_state_group_id',
            $expectedParameterValues['group'],
        );
        $this->assertContainerBuilderHasParameter(
            'netgen_ibexa_scheduled_visibility.object_states.visible.object_state_id',
            $expectedParameterValues['visible'],
        );
        $this->assertContainerBuilderHasParameter(
            'netgen_ibexa_scheduled_visibility.object_states.hidden.object_state_id',
            $expectedParameterValues['hidden'],
        );
    }

    #[DataProvider('provideContentTypesConfigurationCases')]
    public function testContentTypesConfiguration(array $configuration, array $expectedParameterValues): void
    {
        $this->load($configuration);

        $this->assertContainerBuilderHasParameter(
            'netgen_ibexa_scheduled_visibility.content_types.all',
            $expectedParameterValues['all'],
        );
        $this->assertContainerBuilderHasParameter(
            'netgen_ibexa_scheduled_visibility.content_types.allowed',
            $expectedParameterValues['allowed'],
        );
    }

    protected function getContainerExtensions(): array
    {
        return [
            new NetgenIbexaScheduledVisibilityExtension(),
        ];
    }
}
