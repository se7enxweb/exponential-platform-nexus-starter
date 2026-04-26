<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Solr\FieldMapper\ContentTranslation;

use Ibexa\Contracts\Core\Persistence\Content;
use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Contracts\Core\Search\FieldType\StringField;
use Ibexa\Contracts\Solr\FieldMapper\ContentTranslationFieldMapper;

class ContentNameFieldMapper extends ContentTranslationFieldMapper
{
    public function accept(Content $content, $languageCode): bool
    {
        return true;
    }

    public function mapFields(Content $content, $languageCode): array
    {
        if (!isset($content->versionInfo->names[$languageCode])) {
            return [];
        }

        return [
            new Field(
                'ng_content_name',
                $content->versionInfo->names[$languageCode],
                new StringField(),
            ),
        ];
    }
}
