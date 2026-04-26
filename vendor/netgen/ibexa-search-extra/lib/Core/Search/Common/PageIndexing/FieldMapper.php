<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\PageIndexing;

use Ibexa\Contracts\Core\Persistence\Content;
use Ibexa\Contracts\Core\Persistence\Content\Type\Handler as ContentTypeHandler;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Contracts\Core\Search\FieldType\FullTextField;
use Netgen\IbexaSearchExtra\Core\Search\Common\PageIndexing\Exception\MissingConfigException;

final class FieldMapper
{
    public function __construct(
        private readonly TextResolver $textResolver,
        private readonly ContentTypeHandler $contentTypeHandler,
        private readonly ConfigResolver $configResolver,
    ) {}

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function mapFields(Content $content, string $languageCode): array
    {
        $contentInfo = $content->versionInfo->contentInfo;
        $contentType = $this->contentTypeHandler->load($content->versionInfo->contentInfo->contentTypeId);
        $contentTypeIdentifier = $contentType->identifier;

        try {
            $config = $this->configResolver->resolveConfig($contentInfo, $languageCode);
        } catch (MissingConfigException|NotFoundException) {
            return [];
        }

        if (!in_array($contentTypeIdentifier, $config->getAllowedContentTypes(), true)) {
            return [];
        }

        $text = $this->textResolver->resolveText($contentInfo, $languageCode);
        $fields = [];

        foreach ($text as $level => $value) {
            $fields[] = new Field(
                'page_text_' . $level,
                $value,
                new FullTextField(),
            );
        }

        return $fields;
    }
}
