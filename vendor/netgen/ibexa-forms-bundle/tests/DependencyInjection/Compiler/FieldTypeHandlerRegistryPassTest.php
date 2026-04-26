<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaFormsBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\IbexaFormsBundle\DependencyInjection\Compiler\FieldTypeHandlerRegistryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Reference;

final class FieldTypeHandlerRegistryPassTest extends AbstractCompilerPassTestCase
{
    public function testCompilerPassCollectsValidServices(): void
    {
        $registry = new Definition();
        $this->setDefinition('netgen.ibexa_forms.form.fieldtype_handler_registry', $registry);

        $handler = new Definition();
        $handler->addTag('netgen.ibexa_forms.form.fieldtype_handler', ['alias' => 'ibexa_text']);
        $this->setDefinition('netgen.ibexa_forms.form.fieldtype_handler.ibexa_text', $handler);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen.ibexa_forms.form.fieldtype_handler_registry',
            'register',
            [
                'ibexa_text',
                new Reference('netgen.ibexa_forms.form.fieldtype_handler.ibexa_text'),
            ]
        );
    }

    public function testCompilerPassMustThrowExceptionIfHandlerServiceDoesNotHaveAlias(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("'netgen.ibexa_forms.form.fieldtype_handler' service tag needs an 'alias' attribute to identify the field type. None given.");

        $registry = new Definition();
        $this->setDefinition('netgen.ibexa_forms.form.fieldtype_handler_registry', $registry);

        $handler = new Definition();
        $handler->addTag('netgen.ibexa_forms.form.fieldtype_handler');
        $this->setDefinition('netgen.ibexa_forms.form.fieldtype_handler.ibexa_text', $handler);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen.ibexa_forms.form.fieldtype_handler_registry',
            'register',
            [
                new Reference('netgen.ibexa_forms.form.fieldtype_handler.ibexa_text'),
            ]
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new FieldTypeHandlerRegistryPass());
    }
}
