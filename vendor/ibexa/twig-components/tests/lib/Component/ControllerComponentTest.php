<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\TwigComponents\Component;

use Ibexa\TwigComponents\Component\ControllerComponent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class ControllerComponentTest extends TestCase
{
    public function testRenderReturnsSubRequestResponseContent(): void
    {
        $expectedOutput = 'Rendered content';

        $request = new Request();
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack
            ->method('getCurrentRequest')
            ->willReturn($request);

        $response = new Response($expectedOutput);

        $httpKernel = $this->createMock(HttpKernelInterface::class);
        $httpKernel
            ->expects(self::once())
            ->method('handle')
            ->with(self::callback(function (Request $subRequest): bool {
                $this->assertSame('App\Controller\SomeController', $subRequest->attributes->get('_controller'));
                $this->assertSame('bar', $subRequest->attributes->get('foo'));

                return true;
            }))
            ->willReturn($response);

        $component = new ControllerComponent(
            $httpKernel,
            $requestStack,
            'App\Controller\SomeController',
            ['foo' => 'bar']
        );

        $output = $component->render();

        self::assertSame($expectedOutput, $output);
    }

    public function testRenderThrowsWhenNoRequestInStack(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('No current request found in RequestStack');

        $httpKernel = $this->createMock(HttpKernelInterface::class);
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack
            ->method('getCurrentRequest')
            ->willReturn(null);

        $component = new ControllerComponent(
            $httpKernel,
            $requestStack,
            'App\Controller\SomeController'
        );

        $component->render();
    }
}
