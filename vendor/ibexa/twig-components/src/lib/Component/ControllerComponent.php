<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\TwigComponents\Component;

use Ibexa\Contracts\TwigComponents\ComponentInterface;
use LogicException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ControllerComponent implements ComponentInterface
{
    private HttpKernelInterface $httpKernel;

    private RequestStack $requestStack;

    private string $controller;

    /** @var array<mixed> */
    private array $parameters;

    /**
     * @param array<mixed> $parameters
     */
    public function __construct(
        HttpKernelInterface $httpKernel,
        RequestStack $requestStack,
        string $controller,
        array $parameters = []
    ) {
        $this->httpKernel = $httpKernel;
        $this->requestStack = $requestStack;
        $this->controller = $controller;
        $this->parameters = $parameters;
    }

    /**
     * @param array<mixed> $parameters
     */
    public function render(array $parameters = []): string
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request === null) {
            throw new LogicException(
                'No current request found in RequestStack. Ensure that this component is used within an HTTP request context.'
            );
        }

        $attributes = array_merge(
            ['_controller' => $this->controller],
            $this->parameters,
            $parameters
        );

        $subRequest = $request->duplicate([], null, $attributes);
        $response = $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);

        return (string) $response->getContent();
    }
}
