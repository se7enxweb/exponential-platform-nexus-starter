<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\TwigComponents\Component;

use Ibexa\TwigComponents\Component\MenuComponent;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

final class MenuComponentTest extends TestCase
{
    private const EXPECTED_RESULT = '<menu />';
    private const EXAMPLE_MENU_NAME = 'test_menu';
    private const EXAMPLE_MENU_OPTIONS = ['option1' => 'value1', 'option2' => 'value2'];
    private const EXAMPLE_MENU_PATH = ['path1', 'path2'];
    private const EXAMPLE_TEMPLATE = 'menu_template.html.twig';
    private const EXAMPLE_DEPTH = 2;
    private const EXAMPLE_PARAMETERS = [
        'param1' => 'value1',
        'param2' => 'value2',
    ];

    /**
     * @dataProvider dataProviderForTestRender
     *
     * @param array<string, mixed> $options Options passed to the menu component
     * @param array<string> $path Menu item path
     * @param array<string, mixed> $renderOptions Parameters to be passed to the component render method
     * @param array<string, mixed> $expectedTemplateParameters Parameters expected to be passed to the template
     */
    public function testRender(
        string $name,
        array $options,
        array $path,
        ?string $template,
        ?int $depth,
        array $renderOptions,
        array $expectedTemplateParameters
    ): void {
        $twig = $this->createMock(Environment::class);
        $twig
            ->expects(self::once())
            ->method('render')
            ->with(
                '@ibexadesign/twig_components/menu.html.twig',
                $expectedTemplateParameters
            )
            ->willReturn(self::EXPECTED_RESULT);

        $component = new MenuComponent($twig, $name, $options, $path, $template, $depth);
        $output = $component->render($renderOptions);

        self::assertEquals(self::EXPECTED_RESULT, $output);
    }

    /**
     * @return iterable<string, array{
     *     string,
     *     array<string, mixed>,
     *     array<string>,
     *     string|null,
     *     int|null,
     *     array<string, mixed>
     * }>
     */
    public function dataProviderForTestRender(): iterable
    {
        yield 'default' => [
            self::EXAMPLE_MENU_NAME,
            self::EXAMPLE_MENU_OPTIONS,
            self::EXAMPLE_MENU_PATH,
            self::EXAMPLE_TEMPLATE,
            self::EXAMPLE_DEPTH,
            [],
            [
                'name' => self::EXAMPLE_MENU_NAME,
                'options' => self::EXAMPLE_MENU_OPTIONS,
                'path' => self::EXAMPLE_MENU_PATH,
                'renderer_options' => [
                    'template' => self::EXAMPLE_TEMPLATE,
                    'depth' => self::EXAMPLE_DEPTH,
                ],
            ],
        ];

        yield 'merging' => [
            self::EXAMPLE_MENU_NAME,
            self::EXAMPLE_MENU_OPTIONS,
            self::EXAMPLE_MENU_PATH,
            self::EXAMPLE_TEMPLATE,
            self::EXAMPLE_DEPTH,
            self::EXAMPLE_PARAMETERS,
            [
                'name' => self::EXAMPLE_MENU_NAME,
                'options' => self::EXAMPLE_MENU_OPTIONS,
                'path' => self::EXAMPLE_MENU_PATH,
                'renderer_options' => [
                    'template' => self::EXAMPLE_TEMPLATE,
                    'depth' => self::EXAMPLE_DEPTH,
                ],
                'param1' => 'value1',
                'param2' => 'value2',
            ],
        ];
    }
}
