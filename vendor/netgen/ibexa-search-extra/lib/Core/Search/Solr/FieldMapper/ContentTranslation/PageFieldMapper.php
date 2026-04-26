<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Solr\FieldMapper\ContentTranslation;

use Ibexa\Contracts\Core\Persistence\Content;
use Ibexa\Contracts\Solr\FieldMapper\ContentTranslationFieldMapper;
use Netgen\IbexaSearchExtra\Core\Search\Common\PageIndexing\FieldMapper;

class PageFieldMapper extends ContentTranslationFieldMapper
{
    public function __construct(
        private readonly FieldMapper $fieldMapper,
        private readonly bool $isEnabled,
    ) {}

    public function accept(Content $content, $languageCode): bool
    {
        return $this->isEnabled;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function mapFields(Content $content, $languageCode): array
    {
        return $this->fieldMapper->mapFields($content, $languageCode);
    }
}
