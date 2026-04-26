<?php

declare(strict_types=1);

namespace Netgen\IbexaFieldTypeEnhancedLink\Tests\Unit\Persistence\Legacy;

use Ibexa\Contracts\Core\Persistence\Content\FieldTypeConstraints;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition as PersistenceFieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue;
use Netgen\IbexaFieldTypeEnhancedLink\FieldType\Type;
use Netgen\IbexaFieldTypeEnhancedLink\Persistence\Legacy\FieldValueConverter;
use PHPUnit\Framework\TestCase;

/**
 * @group converter
 */
class FieldValueConverterTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject|\Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\RelationConverter */
    protected $converter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->converter = new FieldValueConverter();
    }

    public function testToStorageFieldDefinition(): void
    {
        $fieldDefinition = new PersistenceFieldDefinition(
            [
                'fieldTypeConstraints' => new FieldTypeConstraints(
                    [
                        'fieldSettings' => [
                            'selectionMethod' => Type::SELECTION_DROPDOWN,
                            'selectionRoot' => 12345,
                            'rootDefaultLocation' => false,
                            'selectionContentTypes' => ['article', 'blog_post'],
                            'allowedLinkType' => Type::LINK_TYPE_ALL,
                            'allowedTargetsInternal' => [Type::TARGET_LINK, Type::TARGET_LINK_IN_NEW_TAB, Type::TARGET_EMBED, Type::TARGET_MODAL],
                            'allowedTargetsExternal' => [Type::TARGET_LINK, Type::TARGET_LINK_IN_NEW_TAB],
                            'enableSuffix' => false,
                            'enableLabelInternal' => true,
                            'enableLabelExternal' => true,
                        ],
                    ],
                ),
            ],
        );

        $expectedStorageFieldDefinition = new StorageFieldDefinition();
        $expectedStorageFieldDefinition->dataText5 =
            <<<'DATATEXT'
            {
                "selectionMethod": 1,
                "selectionRoot": 12345,
                "rootDefaultLocation": false,
                "selectionContentTypes": [
                    "article",
                    "blog_post"
                ],
                "allowedLinkType": "all",
                "allowedTargetsInternal": [
                    "link",
                    "link_new_tab",
                    "embed",
                    "modal"
                ],
                "allowedTargetsExternal": [
                    "link",
                    "link_new_tab"
                ],
                "enableSuffix": false,
                "enableLabelInternal": true,
                "enableLabelExternal": true
            }
            DATATEXT;

        $actualStorageFieldDefinition = new StorageFieldDefinition();
        $this->converter->toStorageFieldDefinition($fieldDefinition, $actualStorageFieldDefinition);

        self::assertEquals(
            $expectedStorageFieldDefinition,
            $actualStorageFieldDefinition,
        );
    }

    public function testToFieldDefinition(): void
    {
        $storageFieldDefinition = new StorageFieldDefinition();
        $storageFieldDefinition->dataText5 =
            <<< 'DATATEXT'
            {
                "selectionMethod": 1,
                "selectionRoot": 12345,
                "rootDefaultLocation": false,
                "selectionContentTypes": [
                    "article",
                    "blog_post"
                ],
                "allowedLinkType": "all",
                "allowedTargetsInternal": [
                    "link",
                    "link_new_tab",
                    "embed",
                    "modal"
                ],
                "allowedTargetsExternal": [
                    "link",
                    "link_new_tab"
                ],
                "enableSuffix": false,
                "enableLabelInternal": true,
                "enableLabelExternal": true
            }
            DATATEXT;

        $expectedFieldDefinition = new PersistenceFieldDefinition();
        $expectedFieldDefinition->fieldTypeConstraints = new FieldTypeConstraints(
            [
                'fieldSettings' => [
                    'selectionMethod' => Type::SELECTION_DROPDOWN,
                    'selectionRoot' => 12345,
                    'rootDefaultLocation' => false,
                    'selectionContentTypes' => ['article', 'blog_post'],
                    'allowedLinkType' => Type::LINK_TYPE_ALL,
                    'allowedTargetsInternal' => [Type::TARGET_LINK, Type::TARGET_LINK_IN_NEW_TAB, Type::TARGET_EMBED, Type::TARGET_MODAL],
                    'allowedTargetsExternal' => [Type::TARGET_LINK, Type::TARGET_LINK_IN_NEW_TAB],
                    'enableSuffix' => false,
                    'enableLabelInternal' => true,
                    'enableLabelExternal' => true,
                ],
            ],
        );

        $actualFieldDefinition = new PersistenceFieldDefinition();
        $this->converter->toFieldDefinition($storageFieldDefinition, $actualFieldDefinition);

        self::assertEquals($expectedFieldDefinition, $actualFieldDefinition);
    }

    public function testToFieldDefinitionWithDataText5Null(): void
    {
        $storageFieldDefinition = new StorageFieldDefinition();
        $storageFieldDefinition->dataText5 = null;

        $expectedFieldDefinition = new PersistenceFieldDefinition();
        $expectedFieldDefinition->fieldTypeConstraints = new FieldTypeConstraints(
            [
                'fieldSettings' => [
                    'selectionMethod' => Type::SELECTION_BROWSE,
                    'selectionRoot' => null,
                    'rootDefaultLocation' => false,
                    'selectionContentTypes' => [],
                    'allowedTargetsInternal' => [
                        Type::TARGET_LINK,
                        Type::TARGET_LINK_IN_NEW_TAB,
                        Type::TARGET_EMBED,
                        Type::TARGET_MODAL,
                    ],
                    'allowedTargetsExternal' => [Type::TARGET_LINK, Type::TARGET_LINK_IN_NEW_TAB],
                    'allowedLinkType' => Type::LINK_TYPE_ALL,
                    'enableSuffix' => false,
                    'enableLabelInternal' => true,
                    'enableLabelExternal' => true,
                ],
            ],
        );

        $actualFieldDefinition = new PersistenceFieldDefinition();
        $actualFieldDefinition->fieldTypeConstraints = new FieldTypeConstraints(
            [
                'fieldSettings' => [
                    'selectionMethod' => Type::SELECTION_BROWSE,
                    'selectionRoot' => null,
                    'rootDefaultLocation' => false,
                    'selectionContentTypes' => [],
                    'allowedTargetsInternal' => [
                        Type::TARGET_LINK,
                        Type::TARGET_LINK_IN_NEW_TAB,
                        Type::TARGET_EMBED,
                        Type::TARGET_MODAL,
                    ],
                    'allowedTargetsExternal' => [Type::TARGET_LINK, Type::TARGET_LINK_IN_NEW_TAB],
                    'allowedLinkType' => Type::LINK_TYPE_ALL,
                    'enableSuffix' => false,
                    'enableLabelInternal' => true,
                    'enableLabelExternal' => true,
                ],
            ],
        );

        $this->converter->toFieldDefinition($storageFieldDefinition, $actualFieldDefinition);
        self::assertEquals($expectedFieldDefinition, $actualFieldDefinition);
    }

    public function testToFieldDefinitionWithInvalidDataText5Format(): void
    {
        $this->expectException(\JsonException::class);

        $storageFieldDefinition = new StorageFieldDefinition();
        $storageFieldDefinition->dataText5 = 'String that is not in a valid json format';

        $fieldDefinition = new PersistenceFieldDefinition();
        $this->converter->toFieldDefinition($storageFieldDefinition, $fieldDefinition);
    }

    public function testToFieldValue(): void
    {
        $storageFieldValue = new StorageFieldValue();
        $storageFieldValue->dataText =
            <<< 'DATATEXT'
            {
                "id": 1,
                "label": "Enhanced link",
                "type": "internal",
                "target": "link",
                "suffix": "start",
                "relAttribute": "noreferrer"
            }
            DATATEXT;
        $storageFieldValue->sortKeyString = 'reference';

        $expectedFieldValue = new FieldValue();
        $expectedFieldValue->data = [
            'id' => 1,
            'label' => 'Enhanced link',
            'type' => Type::LINK_TYPE_INTERNAL,
            'target' => Type::TARGET_LINK,
            'suffix' => 'start',
            'relAttribute' => 'noreferrer',
        ];
        $expectedFieldValue->sortKey = 'reference';

        $actualFieldValue = new FieldValue();
        $this->converter->toFieldValue($storageFieldValue, $actualFieldValue);

        self::assertEquals($expectedFieldValue, $actualFieldValue);
    }

    public function testToFieldValueWithDataTextNull(): void
    {
        $storageFieldValue = new StorageFieldValue();
        $storageFieldValue->dataText = null;
        $storageFieldValue->sortKeyString = 'reference';

        $expectedFieldValue = new FieldValue();
        $expectedFieldValue->data = null;
        $expectedFieldValue->sortKey = 'reference';

        $actualFieldValue = new FieldValue();
        $this->converter->toFieldValue($storageFieldValue, $actualFieldValue);

        self::assertEquals($expectedFieldValue, $actualFieldValue);
    }

    public function testToFieldValueWithInvalidDataTextFormat(): void
    {
        $this->expectException(\JsonException::class);

        $storageFieldValue = new StorageFieldValue();
        $storageFieldValue->dataText = 'String that is not in a valid json format';

        $fieldValue = new FieldValue();
        $this->converter->toFieldValue($storageFieldValue, $fieldValue);
    }

    public function testToStorageValue(): void
    {
        $fieldValue = new FieldValue();
        $fieldValue->data = [
            'id' => 1,
            'label' => 'Enhanced link',
            'type' => Type::LINK_TYPE_INTERNAL,
            'target' => Type::TARGET_LINK,
            'suffix' => 'start',
            'relAttribute' => 'noopener',
        ];
        $fieldValue->sortKey = 'reference';

        $expectedStorageFieldValue = new StorageFieldValue();
        $expectedStorageFieldValue->dataText =
            <<< 'DATATEXT'
            {
                "id": 1,
                "label": "Enhanced link",
                "type": "internal",
                "target": "link",
                "suffix": "start",
                "relAttribute": "noopener"
            }
            DATATEXT;
        $expectedStorageFieldValue->sortKeyString = 'reference';

        $actualStorageFieldValue = new StorageFieldValue();
        $this->converter->toStorageValue($fieldValue, $actualStorageFieldValue);

        self::assertEquals($expectedStorageFieldValue, $actualStorageFieldValue);
    }

    public function testToStorageValueWithIdNull(): void
    {
        $fieldValue = new FieldValue();
        $fieldValue->data = [
            'id' => null,
            'label' => 'Enhanced link',
            'type' => Type::LINK_TYPE_EXTERNAL,
            'target' => Type::TARGET_LINK,
            'suffix' => 'start',
            'relAttribute' => null,
        ];
        $fieldValue->sortKey = 'reference';

        $expectedStorageFieldValue = new StorageFieldValue();
        $expectedStorageFieldValue->dataText = null;
        $expectedStorageFieldValue->sortKeyString = 'reference';

        $actualStorageFieldValue = new StorageFieldValue();
        $this->converter->toStorageValue($fieldValue, $actualStorageFieldValue);

        self::assertEquals($expectedStorageFieldValue, $actualStorageFieldValue);
    }

    public function testGetIndexColumn(): void
    {
        $expectedIndexColumn = 'sort_key_string';

        $actualIndexColumn = $this->converter->getIndexColumn();

        self::assertEquals($expectedIndexColumn, $actualIndexColumn);
    }
}
