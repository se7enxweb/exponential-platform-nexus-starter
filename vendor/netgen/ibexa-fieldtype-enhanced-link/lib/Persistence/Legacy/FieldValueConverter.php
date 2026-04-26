<?php

declare(strict_types=1);

namespace Netgen\IbexaFieldTypeEnhancedLink\Persistence\Legacy;

use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue;

use function array_key_exists;
use function json_decode;
use function json_encode;

use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;

class FieldValueConverter implements Converter
{
    /**
     * @throws \JsonException
     */
    public function toStorageValue(FieldValue $value, StorageFieldValue $storageFieldValue): void
    {
        if (empty($value->data['id'])) {
            $storageFieldValue->dataText = null;
        } else {
            $storageFieldValue->dataText = json_encode($value->data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
        }

        $storageFieldValue->sortKeyString = (string) $value->sortKey;
    }

    /**
     * @throws \JsonException
     */
    public function toFieldValue(StorageFieldValue $value, FieldValue $fieldValue): void
    {
        if ($value->dataText) {
            $fieldValue->data = json_decode($value->dataText, true, 512, JSON_THROW_ON_ERROR);
        } else {
            $fieldValue->data = null;
        }

        $fieldValue->sortKey = $value->sortKeyString;
    }

    /**
     * @throws \JsonException
     */
    public function toStorageFieldDefinition(FieldDefinition $fieldDef, StorageFieldDefinition $storageDef): void
    {
        $storageDef->dataText5 = json_encode($fieldDef->fieldTypeConstraints->fieldSettings, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
    }

    /**
     * Converts field definition data in $storageDef into $fieldDef.
     *
     * @throws \JsonException
     */
    public function toFieldDefinition(StorageFieldDefinition $storageDef, FieldDefinition $fieldDef): void
    {
        if (empty($storageDef->dataText5)) {
            return;
        }

        $settingsData = json_decode($storageDef->dataText5, true, 512, JSON_THROW_ON_ERROR);
        $fieldSettings = &$fieldDef->fieldTypeConstraints->fieldSettings;

        if (array_key_exists('selectionMethod', $settingsData)) {
            $fieldSettings['selectionMethod'] = $settingsData['selectionMethod'];
        }

        if (array_key_exists('selectionRoot', $settingsData)) {
            $fieldSettings['selectionRoot'] = $settingsData['selectionRoot'];
        }

        if (array_key_exists('rootDefaultLocation', $settingsData)) {
            $fieldSettings['rootDefaultLocation'] = $settingsData['rootDefaultLocation'];
        }

        if (array_key_exists('selectionContentTypes', $settingsData)) {
            $fieldSettings['selectionContentTypes'] = $settingsData['selectionContentTypes'];
        }

        if (array_key_exists('allowedTargetsInternal', $settingsData)) {
            $fieldSettings['allowedTargetsInternal'] = $settingsData['allowedTargetsInternal'];
        }

        if (array_key_exists('allowedTargetsExternal', $settingsData)) {
            $fieldSettings['allowedTargetsExternal'] = $settingsData['allowedTargetsExternal'];
        }

        if (array_key_exists('allowedLinkType', $settingsData)) {
            $fieldSettings['allowedLinkType'] = $settingsData['allowedLinkType'];
        }

        if (array_key_exists('enableSuffix', $settingsData)) {
            $fieldSettings['enableSuffix'] = $settingsData['enableSuffix'];
        }

        if (array_key_exists('enableLabelInternal', $settingsData)) {
            $fieldSettings['enableLabelInternal'] = $settingsData['enableLabelInternal'];
        }

        if (array_key_exists('enableLabelExternal', $settingsData)) {
            $fieldSettings['enableLabelExternal'] = $settingsData['enableLabelExternal'];
        }
    }

    public function getIndexColumn(): string
    {
        return 'sort_key_string';
    }
}
