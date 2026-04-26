<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaFormsBundle\Tests\Form\DataMapper;

use Ibexa\Core\FieldType\TextLine\Value as TextLineValue;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinitionCollection;
use Ibexa\Core\Repository\Values\User\UserCreateStruct;
use Netgen\Bundle\IbexaFormsBundle\Form\DataMapper;
use Netgen\Bundle\IbexaFormsBundle\Form\DataMapper\CreateUserMapper;
use Netgen\Bundle\IbexaFormsBundle\Form\DataWrapper;
use Netgen\Bundle\IbexaFormsBundle\Form\FieldTypeHandlerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Form\FormConfigBuilder;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

final class CreateUserMapperTest extends TestCase
{
    private CreateUserMapper $mapper;

    private FieldTypeHandlerRegistry $registry;

    private MockObject $handler;

    private MockObject $propertyAccessor;

    protected function setUp(): void
    {
        $this->propertyAccessor = $this->getMockBuilder('Symfony\Component\PropertyAccess\PropertyAccessorInterface')
            ->disableOriginalConstructor()
                        ->getMock();

        $this->handler = $this->getMockBuilder('Netgen\Bundle\IbexaFormsBundle\Form\FieldTypeHandlerInterface')
            ->disableOriginalConstructor()
                        ->getMock();

        $this->registry = new FieldTypeHandlerRegistry();
        $this->registry->register('ibexa_text', $this->handler);
        $this->registry->register('ibexa_user', $this->handler);

        $this->mapper = new CreateUserMapper($this->registry, $this->propertyAccessor);
    }

    public function testInstanceOfDataMapper(): void
    {
        self::assertInstanceOf(DataMapper::class, $this->mapper);
    }

    public function testMapDataToForms(): void
    {
        $contentType = new ContentType(
            [
                'id' => 123,
                'fieldDefinitions' => new FieldDefinitionCollection(
                    [
                        new FieldDefinition(
                            [
                                'id' => 123,
                                'identifier' => 'name',
                                'fieldTypeIdentifier' => 'ibexa_text',
                                'defaultValue' => new TextLineValue('Some name'),
                            ]
                        ),
                    ]
                ),
            ]
        );

        $this->handler->expects(self::once())
            ->method('convertFieldValueToForm')
            ->willReturn('Some name');

        $userCreateStruct = new UserCreateStruct(['contentType' => $contentType]);
        $data = new DataWrapper($userCreateStruct);

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMapped'])
            ->getMock();

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->createMock(PropertyPathInterface::class);

        $propertyPath->expects(self::once())
            ->method('__toString')
            ->willReturn('name');

        $form = $this->getForm();

        $form->expects(self::once())
            ->method('setData');

        $form->expects(self::once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects(self::once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->mapper->mapDataToForms($data, [$form]);
    }

    public function testMapDataToFormsWithInvalidFieldDefinition(): void
    {
        $this->expectException(RuntimeException::class);

        $contentType = new ContentType(
            [
                'id' => 123,
                'fieldDefinitions' => new FieldDefinitionCollection(
                    [
                        new FieldDefinition(
                            [
                                'id' => 123,
                                'identifier' => 'test',
                                'fieldTypeIdentifier' => 'ibexa_text',
                                'defaultValue' => new TextLineValue('Some name'),
                            ]
                        ),
                    ]
                ),
            ]
        );

        $userCreateStruct = new UserCreateStruct(['contentType' => $contentType]);
        $data = new DataWrapper($userCreateStruct);

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMapped'])
            ->getMock();

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->createMock(PropertyPathInterface::class);

        $propertyPath->expects(self::once())
            ->method('__toString')
            ->willReturn('name');

        $form = $this->getForm();

        $form->expects(self::once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects(self::once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->mapper->mapDataToForms($data, [$form]);
    }

    public function testMapFormsToDataFieldTypeIdentifierNotIbexaUser(): void
    {
        $contentType = new ContentType(
            [
                'id' => 123,
                'fieldDefinitions' => new FieldDefinitionCollection(
                    [
                        new FieldDefinition(
                            [
                                'id' => 123,
                                'identifier' => 'name',
                                'fieldTypeIdentifier' => 'ibexa_text',
                                'defaultValue' => new TextLineValue('Some name'),
                            ]
                        ),
                    ]
                ),
            ]
        );

        $this->handler->expects(self::once())
            ->method('convertFieldValueFromForm')
            ->willReturn(new TextLineValue('Some name'));

        $userCreateStruct = new UserCreateStruct([
            'contentType' => $contentType,
            'mainLanguageCode' => 'eng-GB',
        ]);
        $data = new DataWrapper($userCreateStruct);

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMapped'])
            ->getMock();

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->createMock(PropertyPathInterface::class);

        $propertyPath->expects(self::once())
            ->method('__toString')
            ->willReturn('name');

        $form = $this->getForm();

        $form->expects(self::once())
            ->method('isSubmitted')
            ->willReturn(true);

        $form->expects(self::once())
            ->method('isSynchronized')
            ->willReturn(true);

        $form->expects(self::once())
            ->method('isDisabled')
            ->willReturn(false);

        $form->expects(self::once())
            ->method('getData')
            ->willReturn('Some name');

        $form->expects(self::once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects(self::once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->mapper->mapFormsToData([$form], $data);
    }

    public function testMapFormsToData(): void
    {
        $contentType = new ContentType(
            [
                'id' => 123,
                'fieldDefinitions' => new FieldDefinitionCollection(
                    [
                        new FieldDefinition(
                            [
                                'id' => 123,
                                'identifier' => 'name',
                                'fieldTypeIdentifier' => 'ibexa_user',
                                'defaultValue' => new TextLineValue('Some name'),
                            ]
                        ),
                    ]
                ),
            ]
        );

        $formData = [
            'username' => 'username',
            'email' => 'email@test.com',
            'password' => 'passw0rd',
        ];

        $this->handler->expects(self::once())
            ->method('convertFieldValueFromForm')
            ->willReturn($formData);

        $userCreateStruct = new UserCreateStruct(['contentType' => $contentType]);
        $data = new DataWrapper($userCreateStruct);

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMapped'])
            ->getMock();

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->createMock(PropertyPathInterface::class);

        $propertyPath->expects(self::once())
            ->method('__toString')
            ->willReturn('name');

        $form = $this->getForm();

        $form->expects(self::once())
            ->method('isSubmitted')
            ->willReturn(true);

        $form->expects(self::once())
            ->method('isSynchronized')
            ->willReturn(true);

        $form->expects(self::once())
            ->method('isDisabled')
            ->willReturn(false);

        $form->expects(self::once())
            ->method('getData')
            ->willReturn($formData);

        $form->expects(self::once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects(self::once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->mapper->mapFormsToData([$form], $data);
    }

    public function testMapFormsToDataWithInvalidFieldDefinition(): void
    {
        $this->expectException(RuntimeException::class);

        $contentType = new ContentType(
            [
                'id' => 123,
                'fieldDefinitions' => new FieldDefinitionCollection(
                    [
                        new FieldDefinition(
                            [
                                'id' => 123,
                                'identifier' => 'test',
                                'fieldTypeIdentifier' => 'ibexa_user',
                                'defaultValue' => new TextLineValue('Some name'),
                            ]
                        ),
                    ]
                ),
            ]
        );

        $userCreateStruct = new UserCreateStruct(['contentType' => $contentType]);
        $data = new DataWrapper($userCreateStruct);

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMapped'])
            ->getMock();

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->createMock(PropertyPathInterface::class);

        $propertyPath->expects(self::once())
            ->method('__toString')
            ->willReturn('name');

        $form = $this->getForm();

        $form->expects(self::once())
            ->method('isSubmitted')
            ->willReturn(true);

        $form->expects(self::once())
            ->method('isSynchronized')
            ->willReturn(true);

        $form->expects(self::once())
            ->method('isDisabled')
            ->willReturn(false);

        $form->expects(self::once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects(self::once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->mapper->mapFormsToData([$form], $data);
    }

    private function getForm(): MockObject
    {
        return $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->onlyMethods(['getData', 'setData', 'getPropertyPath', 'getConfig', 'isSubmitted', 'isSynchronized', 'isDisabled'])
            ->getMock();
    }
}
