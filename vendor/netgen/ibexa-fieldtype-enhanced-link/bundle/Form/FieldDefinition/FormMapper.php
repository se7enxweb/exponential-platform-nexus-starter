<?php

declare(strict_types=1);

namespace Netgen\IbexaFieldTypeEnhancedLinkBundle\Form\FieldDefinition;

use Ibexa\AdminUi\FieldType\Mapper\AbstractRelationFormMapper;
use Ibexa\AdminUi\Form\Data\FieldDefinitionData;
use Ibexa\ContentForms\Form\Type\RelationType;
use JMS\TranslationBundle\Annotation\Desc;
use Netgen\IbexaFieldTypeEnhancedLink\FieldType\Type;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function array_key_exists;

class FormMapper extends AbstractRelationFormMapper
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data): void
    {
        $isTranslation = $data->contentTypeData->languageCode !== $data->contentTypeData->mainLanguageCode;

        $fieldDefinitionForm->add('allowedLinkType', ChoiceType::class, [
            'choices' => [
                'field_definition.ngenhancedlink.link_type.' . Type::LINK_TYPE_INTERNAL => Type::LINK_TYPE_INTERNAL,
                'field_definition.ngenhancedlink.link_type.' . Type::LINK_TYPE_EXTERNAL => Type::LINK_TYPE_EXTERNAL,
                'field_definition.ngenhancedlink.link_type.' . Type::LINK_TYPE_ALL => Type::LINK_TYPE_ALL,
            ],
            'property_path' => 'fieldSettings[allowedLinkType]',
            'label' => /* @Desc("Allowed link type") */ 'field_definition.ngenhancedlink.selection_allowed_link_type',
            'multiple' => false,
            'expanded' => true,
        ]);

        $fieldDefinitionForm->add('selectionRoot', RelationType::class, [
            'required' => true,
            'property_path' => 'fieldSettings[selectionRoot]',
            'label' => /* @Desc("Starting Location") */ 'field_definition.ngenhancedlink.selection_root',
        ]);

        $fieldDefinitionForm->add('rootDefaultLocation', CheckboxType::class, [
            'required' => false,
            'label' => /* @Desc("Root Default Location") */ 'field_definition.ngenhancedlink.root_default_location',
            'property_path' => 'fieldSettings[rootDefaultLocation]',
        ]);

        $fieldDefinitionForm->add('selectionContentTypes', ChoiceType::class, [
            'choices' => $this->getContentTypesHash(),
            'expanded' => false,
            'multiple' => true,
            'required' => false,
            'property_path' => 'fieldSettings[selectionContentTypes]',
            'label' => /* @Desc("Allowed Content Types") */ 'field_definition.ngenhancedlink.selection_content_types',
            'disabled' => $isTranslation,
        ]);

        $fieldDefinitionForm->add('enableSuffix', CheckboxType::class, [
            'required' => false,
            'label' => /* @Desc("Enable suffix") */ 'field_definition.ngenhancedlink.enable_suffix',
            'property_path' => 'fieldSettings[enableSuffix]',
        ]);

        $fieldDefinitionForm->add('enableLabelInternal', CheckboxType::class, [
            'required' => false,
            'label' => /* @Desc("Enable label") */ 'field_definition.ngenhancedlink.enable_label_internal',
            'property_path' => 'fieldSettings[enableLabelInternal]',
            'data' => $data->fieldSettings && array_key_exists('enableLabelInternal', $data->fieldSettings) ? $data->fieldSettings['enableLabelInternal'] : true,
        ]);

        $fieldDefinitionForm->add('enableLabelExternal', CheckboxType::class, [
            'required' => false,
            'label' => /* @Desc("Enable label") */ 'field_definition.ngenhancedlink.enable_label_external',
            'property_path' => 'fieldSettings[enableLabelExternal]',
            'data' => $data->fieldSettings && array_key_exists('enableLabelExternal', $data->fieldSettings) ? $data->fieldSettings['enableLabelExternal'] : true,
        ]);

        $fieldDefinitionForm->add('allowedTargetsInternal', ChoiceType::class, [
            'choices' => [
                'field_definition.ngenhancedlink.target.' . Type::TARGET_LINK => Type::TARGET_LINK,
                'field_definition.ngenhancedlink.target.' . Type::TARGET_LINK_IN_NEW_TAB => Type::TARGET_LINK_IN_NEW_TAB,
                'field_definition.ngenhancedlink.target.' . Type::TARGET_EMBED => Type::TARGET_EMBED,
                'field_definition.ngenhancedlink.target.' . Type::TARGET_MODAL => Type::TARGET_MODAL,
            ],
            'property_path' => 'fieldSettings[allowedTargetsInternal]',
            'label' => /* @Desc("Allowed Targets Internal") */ 'field_definition.ngenhancedlink.selection_allowed_targets.internal',
            'multiple' => true,
            'expanded' => true,
        ]);

        $fieldDefinitionForm->add('allowedTargetsExternal', ChoiceType::class, [
            'choices' => [
                'field_definition.ngenhancedlink.target.' . Type::TARGET_LINK => Type::TARGET_LINK,
                'field_definition.ngenhancedlink.target.' . Type::TARGET_LINK_IN_NEW_TAB => Type::TARGET_LINK_IN_NEW_TAB,
                'field_definition.ngenhancedlink.target.' . Type::TARGET_EMBED => Type::TARGET_EMBED,
                'field_definition.ngenhancedlink.target.' . Type::TARGET_MODAL => Type::TARGET_MODAL,
            ],
            'property_path' => 'fieldSettings[allowedTargetsExternal]',
            'label' => /* @Desc("Allowed Targets External") */ 'field_definition.ngenhancedlink.selection_allowed_targets.external',
            'multiple' => true,
            'expanded' => true,
        ]);
    }

    /**
     * Fake method to set the translation domain for the extractor.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'content_type',
            ]);
    }
}
