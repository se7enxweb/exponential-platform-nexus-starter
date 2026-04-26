<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Solr\FieldMapper\Content;

use Ibexa\Contracts\Core\Persistence\Content as SPIContent;
use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Contracts\Core\Search\FieldType;
use Ibexa\Contracts\Solr\FieldMapper\ContentFieldMapper;

final class UserEnabledFieldMapper extends ContentFieldMapper
{
    public function accept(SPIContent $content): bool
    {
        return $this->getUserField($content) !== null;
    }

    public function mapFields(SPIContent $content): array
    {
        $userField = $this->getUserField($content);
        if ($userField === null) {
            return [];
        }

        $fields = [];

        if (isset($userField->value->externalData['enabled'])) {
            $fields[] = new Field(
                'ng_user_enabled',
                $userField->value->externalData['enabled'],
                new FieldType\BooleanField(),
            );
        }

        return $fields;
    }

    private function getUserField(SPIContent $content): ?SPIContent\Field
    {
        foreach ($content->fields as $field) {
            if ($field->type === 'ezuser') {
                return $field;
            }
        }

        return null;
    }
}
