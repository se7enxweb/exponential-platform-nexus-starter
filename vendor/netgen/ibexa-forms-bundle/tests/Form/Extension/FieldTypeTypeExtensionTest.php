<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaFormsBundle\Tests\Form\Extension;

use Netgen\Bundle\IbexaFormsBundle\Form\Extension\FieldTypeTypeExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FieldTypeTypeExtensionTest extends TestCase
{
    public function testGetExtendedTypes(): void
    {
        $extension = new FieldTypeTypeExtension();

        self::assertSame([FormType::class], $extension->getExtendedTypes());
    }

    public function testSetDefaultOptions(): void
    {
        $resolver = $this->getMockBuilder(OptionsResolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setDefined'])
            ->getMock();

        $resolver->expects(self::once())
            ->method('setDefined');

        $extension = new FieldTypeTypeExtension();
        $extension->configureOptions($resolver);
    }

    public function testBuildView(): void
    {
        $formView = $this->getMockBuilder(FormView::class)
            ->disableOriginalConstructor()
            ->getMock();

        $form = $this->createMock(FormInterface::class);

        $options = [
            'ibexa_forms' => [
                'fielddefinition' => 'fielddefinition',
                'language_code' => 'language_code',
                'content' => 'content',
                'description' => 'description',
            ],
        ];

        $extension = new FieldTypeTypeExtension();
        $extension->buildView($formView, $form, $options);
    }
}
