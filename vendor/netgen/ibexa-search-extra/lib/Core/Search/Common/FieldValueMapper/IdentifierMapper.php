<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\FieldValueMapper;

use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Contracts\Core\Search\FieldType\IdentifierField;
use Ibexa\Core\Search\Common\FieldValueMapper;
use function preg_replace;

/**
 * Common identifier field value mapper implementation, performing different pattern
 * replacement than the original.
 *
 * @see \Ibexa\Core\Search\Common\FieldValueMapper\IdentifierMapper
 */
final class IdentifierMapper extends FieldValueMapper
{
    public function canMap(Field $field): bool
    {
        return $field->type instanceof IdentifierField;
    }

    public function map(Field $field): string
    {
        return $this->convert($field->value);
    }

    protected function convert($value): string
    {
        // Remove everything except alphanumeric characters, slash (/), underscore (_) and minus (-)
        return preg_replace('([^A-Za-z0-9_\-/]+)', '', (string) $value);
    }
}
