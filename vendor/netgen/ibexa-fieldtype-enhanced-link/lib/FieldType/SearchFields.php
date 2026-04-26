<?php

declare(strict_types=1);

namespace Netgen\IbexaFieldTypeEnhancedLink\FieldType;

use Ibexa\Contracts\Core\FieldType\Indexable;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Contracts\Core\Search;
use Ibexa\Contracts\Core\Search\FieldType\StringField;

use function is_int;

class SearchFields implements Indexable
{
    public function getIndexData(Field $field, FieldDefinition $fieldDefinition): array
    {
        $type = $field->value->data['type'] ?? null;
        $id = $field->value->data['id'] ?? null;

        if ($type === Type::LINK_TYPE_INTERNAL && is_int($id)) {
            return [
                new Search\Field(
                    'value',
                    $id,
                    new StringField(),
                ),
            ];
        }

        return [];
    }

    public function getIndexDefinition(): array
    {
        return [
            'value' => new StringField(),
        ];
    }

    public function getDefaultMatchField(): string
    {
        return 'value';
    }

    public function getDefaultSortField(): string
    {
        return $this->getDefaultMatchField();
    }
}
