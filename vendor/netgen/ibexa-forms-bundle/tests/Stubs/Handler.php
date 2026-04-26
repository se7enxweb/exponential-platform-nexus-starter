<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaFormsBundle\Tests\Stubs;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\IbexaFormsBundle\Form\FieldTypeHandler;

final class Handler extends FieldTypeHandler
{
    /**
     * @return mixed
     */
    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null) {}
}
