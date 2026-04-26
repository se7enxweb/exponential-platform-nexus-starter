<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Elasticsearch\DocumentMapper\BlockTranslationFieldMapper;

use Ibexa\Contracts\Core\Persistence\Content;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Netgen\IbexaSearchExtra\Core\Search\Common\PageIndexing\FieldMapper;
use Netgen\IbexaSearchExtra\Core\Search\Elasticsearch\DocumentMapper\BlockTranslationFieldMapper;

class PageFieldMapper extends BlockTranslationFieldMapper
{
    public function __construct(
        private readonly FieldMapper $fieldMapper,
        private readonly bool $isEnabled,
    ) {}

    public function accept(Content $content, string $languageCode): bool
    {
        return $this->isEnabled;
    }

    /**
     * @throws NotFoundException
     */
    public function mapFields(Content $content, string $languageCode): array
    {
        return $this->fieldMapper->mapFields($content, $languageCode);
    }
}
