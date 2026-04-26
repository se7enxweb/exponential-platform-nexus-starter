<?php

declare(strict_types=1);

namespace Netgen\IbexaFieldTypeEnhancedLinkBundle\Form\Field;

use Ibexa\ContentForms\FieldType\Mapper\AbstractRelationFormMapper;
use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Symfony\Component\Form\FormInterface;

class FieldValueFormMapper extends AbstractRelationFormMapper
{
    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data): void
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig = $fieldForm->getConfig();
        $fieldSettings = $fieldDefinition->getFieldSettings();

        $fieldForm->add(
            $formConfig->getFormFactory()->createBuilder()
                ->create(
                    'value',
                    FieldValueType::class,
                    [
                        'required' => $fieldDefinition->isRequired,
                        'label' => $fieldDefinition->getName(),
                        'default_location' => $this->loadDefaultLocationForSelection(
                            $fieldSettings['selectionRoot'] !== '' ? $fieldSettings['selectionRoot'] : null,
                            $fieldForm->getConfig()->getOption('location'),
                        ),
                        'root_default_location' => $fieldSettings['rootDefaultLocation'] ?? false,
                        'enable_suffix' => $fieldSettings['enableSuffix'] ?? false,
                        'enable_label_internal' => $fieldSettings['enableLabelInternal'] ?? true,
                        'enable_label_external' => $fieldSettings['enableLabelExternal'] ?? true,
                        'target_internal' => $fieldSettings['allowedTargetsInternal'] ?? [],
                        'target_external' => $fieldSettings['allowedTargetsExternal'] ?? [],
                    ],
                )
                ->setAutoInitialize(false)
                ->getForm(),
        );
    }
}
