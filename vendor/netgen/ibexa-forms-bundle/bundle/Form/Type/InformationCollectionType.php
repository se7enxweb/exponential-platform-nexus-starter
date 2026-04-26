<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaFormsBundle\Form\Type;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Netgen\Bundle\IbexaFormsBundle\Form\DataWrapper;
use Netgen\Bundle\IbexaFormsBundle\Form\FieldTypeHandlerRegistry;
use Netgen\Bundle\IbexaFormsBundle\Form\Payload\InformationCollectionStruct;
use RuntimeException;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilderInterface;

use function array_keys;
use function in_array;

final class InformationCollectionType extends AbstractContentType
{
    protected ConfigResolverInterface $configResolver;

    public function __construct(
        FieldTypeHandlerRegistry $fieldTypeHandlerRegistry,
        DataMapperInterface $dataMapper,
        ConfigResolverInterface $configResolver
    ) {
        parent::__construct($fieldTypeHandlerRegistry, $dataMapper);

        $this->configResolver = $configResolver;
    }

    public function getBlockPrefix(): string
    {
        return 'ibexa_forms_information_collection';
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var DataWrapper $dataWrapper */
        $dataWrapper = $options['data'];

        if (!$dataWrapper instanceof DataWrapper) {
            throw new RuntimeException(
                'Data must be an instance of Netgen\IbexaFormsBundle\Form\DataWrapper'
            );
        }

        /** @var InformationCollectionStruct $payload */
        $payload = $dataWrapper->payload;

        if (!$payload instanceof InformationCollectionStruct) {
            throw new RuntimeException(
                'Data payload must be an instance of Netgen\Bundle\IbexaFormsBundle\Form\Payload\InformationCollectionStruct'
            );
        }

        /** @var ContentType $contentType */
        $contentType = $dataWrapper->definition;

        if (!$contentType instanceof ContentType) {
            throw new RuntimeException(
                'Data definition must be an instance of Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType'
            );
        }

        $builder->setDataMapper($this->dataMapper);

        foreach ($contentType->getFieldDefinitions() as $fieldDefinition) {
            if ($fieldDefinition->fieldTypeIdentifier === 'ibexa_user') {
                continue;
            }

            if (!$fieldDefinition->isInfoCollector) {
                continue;
            }

            $handler = $this->fieldTypeHandlerRegistry->get($fieldDefinition->fieldTypeIdentifier);
            $handler->buildFieldCreateForm($builder, $fieldDefinition, $this->getLanguageCode($contentType));
        }
    }

    /**
     * If ContentType language code is in languages array then use it, else use first available one.
     */
    protected function getLanguageCode(ContentType $contentType): string
    {
        $contentTypeLanguages = array_keys($contentType->getNames());

        foreach ($this->configResolver->getParameter('languages') as $languageCode) {
            if (in_array($languageCode, $contentTypeLanguages, true)) {
                return $languageCode;
            }
        }

        return $contentType->mainLanguageCode;
    }
}
