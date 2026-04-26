<?php

declare(strict_types=1);

namespace Netgen\IbexaFieldTypeEnhancedLink\FieldType;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\Handler as SPIContentHandler;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\RelationType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\FieldType;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Core\FieldType\Value as BaseValue;

use function array_intersect;
use function count;
use function in_array;
use function is_array;
use function is_bool;
use function is_int;
use function is_string;

class Type extends FieldType
{
    public const int SELECTION_BROWSE = 0;
    public const int SELECTION_DROPDOWN = 1;
    public const string LINK_TYPE_EXTERNAL = 'external';
    public const string LINK_TYPE_INTERNAL = 'internal';
    public const string LINK_TYPE_ALL = 'all';
    public const string TARGET_LINK = 'link';
    public const string TARGET_EMBED = 'embed';
    public const string TARGET_MODAL = 'modal';
    public const string TARGET_LINK_IN_NEW_TAB = 'link_new_tab';

    protected $settingsSchema = [
        'selectionMethod' => [
            'type' => 'int',
            'default' => self::SELECTION_BROWSE,
        ],
        'selectionRoot' => [
            'type' => 'string',
            'default' => null,
        ],
        'rootDefaultLocation' => [
            'type' => 'bool',
            'default' => false,
        ],
        'selectionContentTypes' => [
            'type' => 'array',
            'default' => [],
        ],
        'allowedLinkType' => [
            'type' => 'choice',
            'default' => self::LINK_TYPE_ALL,
        ],
        'allowedTargetsInternal' => [
            'type' => 'array',
            'default' => [
                self::TARGET_LINK,
                self::TARGET_LINK_IN_NEW_TAB,
                self::TARGET_EMBED,
                self::TARGET_MODAL,
            ],
        ],
        'allowedTargetsExternal' => [
            'type' => 'array',
            'default' => [
                self::TARGET_LINK,
                self::TARGET_LINK_IN_NEW_TAB,
            ],
        ],
        'enableSuffix' => [
            'type' => 'bool',
            'default' => true,
        ],
        'enableLabelInternal' => [
            'type' => 'bool',
            'default' => true,
        ],
        'enableLabelExternal' => [
            'type' => 'bool',
            'default' => true,
        ],
    ];

    public function __construct(
        private SPIContentHandler $handler,
        private InternalLinkValidator $targetContentValidator,
    ) {}

    public function validateFieldSettings($fieldSettings): array
    {
        $validationErrors = [];

        foreach ($fieldSettings as $name => $value) {
            if (!isset($this->settingsSchema[$name])) {
                $validationErrors[] = new ValidationError(
                    "Setting '%setting%' is unknown",
                    null,
                    [
                        '%setting%' => $name,
                    ],
                    "[{$name}]",
                );

                continue;
            }

            switch ($name) {
                case 'selectionMethod':
                    if ($value !== self::SELECTION_BROWSE && $value !== self::SELECTION_DROPDOWN) {
                        $validationErrors[] = new ValidationError(
                            "Setting '%setting%' must be either %selection_browse% or %selection_dropdown%",
                            null,
                            [
                                '%setting%' => $name,
                                '%selection_browse%' => self::SELECTION_BROWSE,
                                '%selection_dropdown%' => self::SELECTION_DROPDOWN,
                            ],
                            "[{$name}]",
                        );
                    }

                    break;

                case 'selectionRoot':
                    if (!is_int($value) && !is_string($value) && $value !== null) {
                        $validationErrors[] = new ValidationError(
                            "Setting '%setting%' value must be of either null, string or integer",
                            null,
                            [
                                '%setting%' => $name,
                            ],
                            "[{$name}]",
                        );
                    }

                    break;

                case 'rootDefaultLocation':
                case 'enableSuffix':
                case 'enableLabelExternal':
                case 'enableLabelInternal':
                    if (!is_bool($value)) {
                        $validationErrors[] = new ValidationError(
                            "Setting '%setting%' value must be of boolean type",
                            null,
                            [
                                '%setting%' => $name,
                            ],
                            "[{$name}]",
                        );
                    }

                    break;

                case 'selectionContentTypes':
                    if (!is_array($value)) {
                        $validationErrors[] = new ValidationError(
                            "Setting '%setting%' value must be of array type",
                            null,
                            [
                                '%setting%' => $name,
                            ],
                            "[{$name}]",
                        );
                    }

                    break;

                case 'allowedLinkType':
                    if (!in_array($value, [self::LINK_TYPE_INTERNAL, self::LINK_TYPE_EXTERNAL, self::LINK_TYPE_ALL], true)) {
                        $validationErrors[] = new ValidationError(
                            "Setting '%setting%' value must be %external%, %internal% or %all%",
                            null,
                            [
                                '%setting%' => $name,
                                '%external%' => self::LINK_TYPE_EXTERNAL,
                                '%internal%' => self::LINK_TYPE_INTERNAL,
                                '%all%' => self::LINK_TYPE_ALL,
                            ],
                            "[{$name}]",
                        );
                    }

                    break;

                case 'allowedTargetsExternal':
                case 'allowedTargetsInternal':
                    if (!is_array($value) || count(array_intersect($value, [self::TARGET_LINK, self::TARGET_LINK_IN_NEW_TAB, self::TARGET_EMBED, self::TARGET_MODAL])) === 0) {
                        $validationErrors[] = new ValidationError(
                            "Setting '%setting%' value must be one or either %link%, %link_in_new_tab%, %in_place% and/or %modal%",
                            null,
                            [
                                '%setting%' => $name,
                                '%link%' => self::TARGET_LINK,
                                '%link_in_new_tab%' => self::TARGET_LINK_IN_NEW_TAB,
                                '%in_place%' => self::TARGET_EMBED,
                                '%modal%' => self::TARGET_MODAL,
                            ],
                            "[{$name}]",
                        );
                    }

                    break;
            }
        }

        return $validationErrors;
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ngenhancedlink';
    }

    public function getName(SPIValue $value, FieldDefinition $fieldDefinition, string $languageCode): string
    {
        /** @var Value $value */
        if ($value->isTypeExternal()) {
            return (string) $value->label;
        }

        if ($value->isTypeInternal()) {
            try {
                $contentInfo = $this->handler->loadContentInfo($value->reference);
                $versionInfo = $this->handler->loadVersionInfo($value->reference, $contentInfo->currentVersionNo);
            } catch (NotFoundException $e) {
                return '';
            }

            return $versionInfo->names[$languageCode] ?? $versionInfo->names[$contentInfo->mainLanguageCode];
        }

        return '';
    }

    public function validate(FieldDefinition $fieldDefinition, SPIValue $value): array
    {
        /** @var Value $value */
        $validationErrors = [];

        if ($this->isEmptyValue($value)) {
            return $validationErrors;
        }

        $allowedLinkType = $fieldDefinition->getFieldSettings()['allowedLinkType'] ?? null;

        if (($allowedLinkType === self::LINK_TYPE_EXTERNAL && !$value->isTypeExternal()) || ($allowedLinkType === self::LINK_TYPE_INTERNAL && !$value->isTypeInternal())) {
            $validationErrors[] = new ValidationError(
                'Link type is not allowed. Must be of type %type%',
                null,
                [
                    '%type%' => $allowedLinkType,
                ],
                'allowedLinkType',
            );
        }

        if ($value->isTypeInternal()) {
            $allowedTargetsInternal = $fieldDefinition->getFieldSettings()['allowedTargetsInternal'] ?? [];
            if (!empty($allowedTargetsInternal) && !in_array($value->target, $allowedTargetsInternal, true)) {
                $validationErrors[] = new ValidationError(
                    'Target %target% is not a valid target',
                    null,
                    [
                        '%target%' => $value->target,
                    ],
                    'allowedTargetsInternal',
                );
            }
        }

        if ($value->isTypeExternal()) {
            $allowedTargetsExternal = $fieldDefinition->getFieldSettings()['allowedTargetsExternal'] ?? [];
            if (!empty($allowedTargetsExternal) && !in_array($value->target, $allowedTargetsExternal, true)) {
                $validationErrors[] = new ValidationError(
                    'Target %target% is not a valid target',
                    null,
                    [
                        '%target%' => $value->target,
                    ],
                    'allowedTargetsExternal',
                );
            }
        }

        $allowedContentTypes = $fieldDefinition->getFieldSettings()['selectionContentTypes'] ?? [];
        $validationError = $this->targetContentValidator->validate(
            $value,
            $allowedContentTypes,
        );

        if (isset($validationError)) {
            $validationErrors[] = $validationError;
        }

        return $validationErrors;
    }

    public function getEmptyValue(): Value
    {
        return new Value();
    }

    public function isEmptyValue(SPIValue $value): bool
    {
        /* @var Value $value */
        return !isset($value->reference);
    }

    public function fromHash($hash): Value
    {
        if ($hash !== null) {
            $reference = $hash['reference'];

            if (isset($reference)) {
                if (is_int($reference)) {
                    try {
                        $contentInfo = $this->handler->loadContentInfo($reference);
                        $versionInfo = $this->handler->loadVersionInfo($reference, $contentInfo->currentVersionNo);
                    } catch (NotFoundException $e) {
                        return $this->getEmptyValue();
                    }
                }

                return new Value($reference, $hash['label'], $hash['target'], $hash['suffix'], $hash['rel_attribute']);
            }
        }

        return $this->getEmptyValue();
    }

    public function toHash(SPIValue $value): array
    {
        /* @var Value $value */
        return [
            'reference' => $value->reference,
            'label' => $value->label,
            'target' => $value->target,
            'suffix' => $value->suffix,
            'rel_attribute' => $value->relAttribute,
        ];
    }

    public function isSearchable(): bool
    {
        return true;
    }

    public function getRelations(SPIValue $fieldValue): array
    {
        /** @var Value $fieldValue */
        $relations = [];
        if ($fieldValue->isTypeInternal()) {
            $relations[RelationType::FIELD->value] = [$fieldValue->reference];
        }

        return $relations;
    }

    public function toPersistenceValue(SPIValue $value): FieldValue
    {
        /** @var Value $value */
        if ($value->isTypeExternal()) {
            return new FieldValue([
                'data' => [
                    'id' => null,
                    'label' => $value->label,
                    'type' => self::LINK_TYPE_EXTERNAL,
                    'target' => $value->target,
                    'suffix' => $value->suffix,
                    'rel_attribute' => $value->relAttribute,
                ],
                'externalData' => $value->reference,
                'sortKey' => $this->getSortInfo($value),
            ]);
        }

        if ($value->isTypeInternal()) {
            return new FieldValue([
                'data' => [
                    'id' => $value->reference,
                    'label' => $value->label,
                    'type' => self::LINK_TYPE_INTERNAL,
                    'target' => $value->target,
                    'suffix' => $value->suffix,
                    'rel_attribute' => $value->relAttribute,
                ],
                'externalData' => null,
                'sortKey' => $this->getSortInfo($value),
            ]);
        }

        return new FieldValue([
            'data' => [],
            'externalData' => null,
            'sortKey' => null,
        ]);
    }

    public function fromPersistenceValue(FieldValue $fieldValue): Value
    {
        $id = $fieldValue->data['id'] ?? null;
        $type = $fieldValue->data['type'] ?? null;
        $label = $fieldValue->data['label'] ?? null;
        $target = $fieldValue->data['target'] ?? self::TARGET_LINK;
        $suffix = $fieldValue->data['suffix'] ?? null;
        $relAttribute = $fieldValue->data['rel_attribute'] ?? null;

        if ($type === self::LINK_TYPE_INTERNAL && is_int($id)) {
            try {
                $contentInfo = $this->handler->loadContentInfo($id);
                $versionInfo = $this->handler->loadVersionInfo($id, $contentInfo->currentVersionNo);
            } catch (NotFoundException $e) {
                return $this->getEmptyValue();
            }

            return new Value(
                $id,
                $label,
                $target,
                $suffix,
                $relAttribute,
            );
        }

        if ($type === self::LINK_TYPE_EXTERNAL && is_string($fieldValue->externalData)) {
            return new Value(
                $fieldValue->externalData,
                $label,
                $target,
                $suffix,
                $relAttribute,
            );
        }

        return $this->getEmptyValue();
    }

    protected function createValueFromInput($inputValue)
    {
        if ($inputValue instanceof ContentInfo) {
            return new Value($inputValue->id);
        }

        if (is_int($inputValue) || is_string($inputValue)) {
            return new Value($inputValue);
        }

        return $inputValue;
    }

    protected function checkValueStructure(BaseValue $value): void
    {
        /** @var \Netgen\IbexaFieldTypeEnhancedLink\FieldType\Value $value */
        if (!$value->isTypeInternal() && !$value->isTypeExternal()) {
            throw new InvalidArgumentType(
                '$value->reference',
                'int|string',
                $value->reference,
            );
        }
    }

    protected function getSortInfo(BaseValue $value): string
    {
        return (string) $value;
    }
}
