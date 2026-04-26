<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaFormsBundle\Tests\Form;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Form\FormBuilderInterface;

final class CustomFieldTypeHandlerTest extends TestCase
{
    public function testBuildFieldUpdateFormWhenNoImplementedHandler(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not implemented.');

        $formBuilder = $this->createMock(FormBuilderInterface::class);
        $fieldDefinition = $this->getMockBuilder(FieldDefinition::class)
            ->disableOriginalConstructor()
            ->getMock();

        $content = $this->createMock(Content::class);
        $language = 'eng-GB';

        $handler = new CustomFieldTypeHandler();
        $handler->buildFieldUpdateForm($formBuilder, $fieldDefinition, $content, $language);
    }
}
