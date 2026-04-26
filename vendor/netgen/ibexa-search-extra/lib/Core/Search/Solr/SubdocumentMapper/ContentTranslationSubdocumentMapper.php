<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Solr\SubdocumentMapper;

use Ibexa\Contracts\Core\Persistence\Content;

/**
 * Maps Content in a specific language to an array of subdocuments.
 */
abstract class ContentTranslationSubdocumentMapper
{
    /**
     * Indicate if the mapper accepts the given $content for mapping.
     */
    abstract public function accept(Content $content, string $languageCode): bool;

    /**
     * Maps given Content to a Document.
     *
     * @return \Ibexa\Contracts\Core\Search\Document[]
     */
    abstract public function mapDocuments(Content $content, string $languageCode): array;
}
