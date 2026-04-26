<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Tests\Integration\Implementation\Stubs;

use RuntimeException;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

class RouterStub implements RouterInterface
{
    public function setContext(RequestContext $context): void
    {
        throw new RuntimeException('Not implemented');
    }

    public function getContext(): void
    {
        throw new RuntimeException('Not implemented');
    }

    public function getRouteCollection(): void
    {
        throw new RuntimeException('Not implemented');
    }

    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): void
    {
        throw new RuntimeException('Not implemented');
    }

    public function match(string $pathinfo): void
    {
        throw new RuntimeException('Not implemented');
    }
}
