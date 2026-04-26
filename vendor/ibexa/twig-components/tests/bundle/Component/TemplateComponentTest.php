<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\TwigComponents\Component;

use Ibexa\TwigComponents\Component\TemplateComponent;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

final class TemplateComponentTest extends TestCase
{
    public function testRenderWithoutParameters(): void
    {
        $twig = $this->configureTwig([
            '__parameter__' => true,
        ]);

        $component = new TemplateComponent(
            $twig,
            '__template__',
            [
                '__parameter__' => true,
            ],
        );

        $component->render();
    }

    public function testRenderWithParameters(): void
    {
        $twig = $this->configureTwig([
            '__parameter__' => false,
        ]);

        $component = new TemplateComponent(
            $twig,
            '__template__',
            [
                '__parameter__' => true,
            ],
        );

        $component->render([
            '__parameter__' => false,
        ]);
    }

    public function testRenderWithNewParameter(): void
    {
        $twig = $this->configureTwig([
            '__parameter__' => true,
            '__new_parameter__' => 'foo',
        ]);

        $component = new TemplateComponent(
            $twig,
            '__template__',
            [
                '__parameter__' => true,
            ],
        );

        $component->render([
            '__new_parameter__' => 'foo',
        ]);
    }

    /**
     * @param array<mixed> $parameters
     *
     * @return \Twig\Environment|\PHPUnit\Framework\MockObject\MockObject
     */
    private function configureTwig(array $parameters): Environment
    {
        $twig = $this->createMock(Environment::class);
        $twig->expects(self::once())
            ->method('render')
            ->with(
                '__template__',
                $parameters,
            );

        return $twig;
    }
}
