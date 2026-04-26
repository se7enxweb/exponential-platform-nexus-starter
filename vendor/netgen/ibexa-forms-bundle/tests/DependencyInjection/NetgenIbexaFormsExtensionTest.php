<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaFormsBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Netgen\Bundle\IbexaFormsBundle\DependencyInjection\NetgenIbexaFormsExtension;

final class NetgenIbexaFormsExtensionTest extends AbstractExtensionTestCase
{
    public function testItSetsValidContainerParameters(): void
    {
        $this->container->setParameter('ibexa.site_access.list', []);
        $this->load();
    }

    protected function getContainerExtensions(): array
    {
        return [
            new NetgenIbexaFormsExtension(),
        ];
    }

    protected function getMinimalConfiguration(): array
    {
        return [];
    }
}
