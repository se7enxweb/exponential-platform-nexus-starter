<?php

declare(strict_types=1);

namespace Netgen\IbexaFieldTypeEnhancedLink\FieldType;

use Ibexa\Core\FieldType\Value as BaseValue;

use function is_int;
use function is_string;

class Value extends BaseValue
{
    /**
     * @noinspection MagicMethodsValidityInspection
     * @noinspection PhpMissingParentConstructorInspection
     *
     * @param ?int|?string $reference
     */
    public function __construct(
        public $reference = null,
        public ?string $label = null,
        public string $target = Type::TARGET_LINK,
        public ?string $suffix = null,
        public ?string $relAttribute = null,
    ) {}

    public function __toString(): string
    {
        if (is_string($this->reference)) {
            return $this->reference;
        }

        if (is_int($this->reference)) {
            return (string) $this->reference;
        }

        return '';
    }

    public function isTypeExternal(): bool
    {
        return is_string($this->reference);
    }

    public function isTypeInternal(): bool
    {
        return is_int($this->reference);
    }

    public function isTargetModal(): bool
    {
        return $this->target === Type::TARGET_MODAL;
    }

    public function isTargetEmbed(): bool
    {
        return $this->target === Type::TARGET_EMBED;
    }

    public function isTargetLink(): bool
    {
        return $this->target === Type::TARGET_LINK;
    }

    public function isTargetLinkInNewTab(): bool
    {
        return $this->target === Type::TARGET_LINK_IN_NEW_TAB;
    }
}
