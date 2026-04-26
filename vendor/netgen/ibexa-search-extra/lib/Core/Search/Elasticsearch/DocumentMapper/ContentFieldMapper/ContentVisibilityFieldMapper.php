<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Elasticsearch\DocumentMapper\ContentFieldMapper;

use Ibexa\Contracts\Core\Persistence\Content as SPIContent;
use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Contracts\Core\Search\FieldType\BooleanField;
use Netgen\IbexaSearchExtra\Core\Search\Elasticsearch\DocumentMapper\ContentFieldMapper;

final class ContentVisibilityFieldMapper extends ContentFieldMapper
{
    public function accept(SPIContent $content): bool
    {
        return true;
    }

    public function mapFields(SPIContent $content): array
    {
        return [
            new Field(
                'ng_content_visible',
                !$content->versionInfo->contentInfo->isHidden,
                new BooleanField(),
            ),
        ];
    }
}
