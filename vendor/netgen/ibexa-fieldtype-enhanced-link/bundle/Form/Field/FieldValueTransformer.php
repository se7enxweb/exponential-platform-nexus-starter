<?php

declare(strict_types=1);

namespace Netgen\IbexaFieldTypeEnhancedLinkBundle\Form\Field;

use Ibexa\Contracts\Core\Repository\FieldType;
use Netgen\IbexaFieldTypeEnhancedLink\FieldType\Type;
use Netgen\IbexaFieldTypeEnhancedLink\FieldType\Value;
use Symfony\Component\Form\DataTransformerInterface;

class FieldValueTransformer implements DataTransformerInterface
{
    private FieldType $fieldType;

    public function __construct(FieldType $fieldType)
    {
        $this->fieldType = $fieldType;
    }

    public function transform($value): ?array
    {
        if (!$value instanceof Value) {
            return null;
        }

        if ($value->isTypeExternal()) {
            return [
                'link_type' => Type::LINK_TYPE_EXTERNAL,
                'url' => $value->reference,
                'label_external' => $value->label,
                'target_external' => $value->target,
                'rel_attribute' => $value->relAttribute,
            ];
        }

        if ($value->isTypeInternal()) {
            return [
                'link_type' => Type::LINK_TYPE_INTERNAL,
                'id' => $value->reference,
                'label_internal' => $value->label,
                'target_internal' => $value->target,
                'suffix' => $value->suffix,
                'rel_attribute' => $value->relAttribute,
            ];
        }

        return null;
    }

    public function reverseTransform($value): ?Value
    {
        if ($value['link_type'] === Type::LINK_TYPE_INTERNAL) {
            return new Value(
                $value['id'],
                $value['label_internal'] ?? null,
                $value['target_internal'],
                $value['suffix'] ?? null,
                $value['rel_attribute'] ?? null,
            );
        }

        if ($value['link_type'] === Type::LINK_TYPE_EXTERNAL) {
            return new Value(
                $value['url'],
                $value['label_external'] ?? null,
                $value['target_external'],
                null,
                $value['rel_attribute'] ?? null,
            );
        }

        return $this->fieldType->getEmptyValue();
    }
}
