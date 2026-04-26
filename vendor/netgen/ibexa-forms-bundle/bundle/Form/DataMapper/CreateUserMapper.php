<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaFormsBundle\Form\DataMapper;

use Netgen\Bundle\IbexaFormsBundle\Form\DataMapper;
use Netgen\Bundle\IbexaFormsBundle\Form\DataWrapper;
use RuntimeException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

/**
 * A data mapper using property paths to read/write data.
 */
final class CreateUserMapper extends DataMapper
{
    protected function mapToForm(FormInterface $form, DataWrapper $data, PropertyPathInterface $propertyPath): void
    {
        /** @var \Ibexa\Core\Repository\Values\User\UserCreateStruct $userCreateStruct */
        $userCreateStruct = $data->payload;
        $contentType = $userCreateStruct->contentType;

        $fieldDefinitionIdentifier = (string) $propertyPath;
        $fieldDefinition = $contentType->getFieldDefinition($fieldDefinitionIdentifier);

        if (null === $fieldDefinition) {
            throw new RuntimeException(
                "Data payload does not contain expected FieldDefinition '{$fieldDefinitionIdentifier}'"
            );
        }

        $fieldTypeIdentifier = $fieldDefinition->fieldTypeIdentifier;

        $handler = $this->fieldTypeHandlerRegistry->get($fieldTypeIdentifier);
        $form->setData(
            $handler->convertFieldValueToForm(
                $contentType->getFieldDefinition($fieldDefinitionIdentifier)->defaultValue,
                $fieldDefinition
            )
        );
    }

    protected function mapFromForm(FormInterface $form, DataWrapper $data, PropertyPathInterface $propertyPath): void
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\User\UserCreateStruct $userCreateStruct */
        $userCreateStruct = $data->payload;
        $contentType = $userCreateStruct->contentType;

        $fieldDefinitionIdentifier = (string) $propertyPath;
        $fieldDefinition = $contentType->getFieldDefinition($fieldDefinitionIdentifier);

        if (null === $fieldDefinition) {
            throw new RuntimeException(
                "Data payload does not contain expected FieldDefinition '{$fieldDefinitionIdentifier}'"
            );
        }

        $fieldTypeIdentifier = $fieldDefinition->fieldTypeIdentifier;

        $handler = $this->fieldTypeHandlerRegistry->get($fieldTypeIdentifier);
        $convertedData = $handler->convertFieldValueFromForm($form->getData());

        // 'ibexa_user' is an exceptional case, here we need to map form data to the properties
        // in the UserCreateStruct
        if ($fieldTypeIdentifier === 'ibexa_user') {
            $userCreateStruct->login = $convertedData['username'];
            $userCreateStruct->email = $convertedData['email'];
            $userCreateStruct->password = $convertedData['password'] ?? null;
        } else {
            $userCreateStruct->setField($fieldDefinitionIdentifier, $convertedData);
        }
    }
}
