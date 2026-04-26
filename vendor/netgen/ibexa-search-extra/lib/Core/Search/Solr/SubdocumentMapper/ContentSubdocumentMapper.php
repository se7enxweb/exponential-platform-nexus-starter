<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Solr\SubdocumentMapper;

use Ibexa\Contracts\Core\Persistence\Content;

/**
 * Maps Content to an array of subdocuments.
 */
abstract class ContentSubdocumentMapper
{
    /**
     * Indicate if the mapper accepts the given $content for mapping.
     */
    abstract public function accept(Content $content): bool;

    /**
     * Maps given Content to an array of Documents.
     *
     * @return \Ibexa\Contracts\Core\Search\Document[]
     */
    abstract public function mapDocuments(Content $content): array;
}
