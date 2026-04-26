<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\TwigComponent\DependencyInjection\Compiler;

use Ibexa\Bundle\TwigComponents\DependencyInjection\Compiler\ComponentPass;
use Ibexa\Contracts\TwigComponents\Exception\InvalidArgumentException;
use Ibexa\TwigComponents\Component\Registry;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class ComponentPassTest extends AbstractCompilerPassTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setDefinition(Registry::class, new Definition());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ComponentPass());
    }

    public function testProcess(): void
    {
        $taggedServiceId = 'collected_service';
        $collectedService = new Definition();
        $collectedService->addTag(ComponentPass::TAG_NAME, ['group' => 'someGroup']);
        $this->setDefinition($taggedServiceId, $collectedService);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            $taggedServiceId,
            ComponentPass::TAG_NAME,
            ['group' => 'someGroup']
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            Registry::class,
            'addComponent',
            ['someGroup', $taggedServiceId, new Reference($taggedServiceId)]
        );
    }

    public function testProcessWithNoGroup(): void
    {
        $taggedServiceId = 'collected_service';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Argument \'%s\' is invalid: Tag %s must contain a "group" argument.',
                $taggedServiceId,
                ComponentPass::TAG_NAME,
            )
        );

        $collectedService = new Definition();
        $collectedService->addTag(ComponentPass::TAG_NAME);
        $this->setDefinition($taggedServiceId, $collectedService);

        $this->compile();
    }
}
