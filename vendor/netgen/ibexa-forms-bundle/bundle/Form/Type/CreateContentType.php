<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaFormsBundle\Form\Type;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct;
use Netgen\Bundle\IbexaFormsBundle\Form\DataWrapper;
use RuntimeException;
use Symfony\Component\Form\FormBuilderInterface;

final class CreateContentType extends AbstractContentType
{
    public function getBlockPrefix(): string
    {
        return 'ibexa_forms_create_content';
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var \Netgen\Bundle\IbexaFormsBundle\Form\DataWrapper $dataWrapper */
        $dataWrapper = $options['data'];

        if (!$dataWrapper instanceof DataWrapper) {
            throw new RuntimeException(
                'Data must be an instance of Netgen\IbexaFormsBundle\Form\DataWrapper'
            );
        }

        $contentCreateStruct = $dataWrapper->payload;

        if (!$contentCreateStruct instanceof ContentCreateStruct) {
            throw new RuntimeException(
                'Data payload must be an instance of Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct'
            );
        }

        $builder->setDataMapper($this->dataMapper);

        foreach ($contentCreateStruct->contentType->getFieldDefinitions() as $fieldDefinition) {
            // Users can't be created through Content, if ibexa_user field is found we simply skip it
            if ($fieldDefinition->fieldTypeIdentifier === 'ibexa_user') {
                continue;
            }

            $handler = $this->fieldTypeHandlerRegistry->get($fieldDefinition->fieldTypeIdentifier);
            $handler->buildFieldCreateForm($builder, $fieldDefinition, $contentCreateStruct->mainLanguageCode);
        }

        // Intentionally omitting submit buttons, set them manually as needed
    }
}
