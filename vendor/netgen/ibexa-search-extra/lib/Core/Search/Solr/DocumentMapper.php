<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Solr;

use Ibexa\Contracts\Core\Persistence\Content;
use Ibexa\Contracts\Solr\DocumentMapper as DocumentMapperInterface;
use Netgen\IbexaSearchExtra\Core\Search\Solr\SubdocumentMapper\ContentSubdocumentMapper;
use Netgen\IbexaSearchExtra\Core\Search\Solr\SubdocumentMapper\ContentTranslationSubdocumentMapper;
use function array_merge;
use function preg_replace;

/**
 * This DocumentMapper implementation adds support for indexing custom Content subdocuments.
 *
 * @see \Ibexa\Contracts\Solr\DocumentMapper
 * @see \Netgen\IbexaSearchExtra\Core\Search\Solr\SubdocumentMapper\ContentSubdocumentMapper
 * @see \Netgen\IbexaSearchExtra\Core\Search\Solr\SubdocumentMapper\ContentTranslationSubdocumentMapper
 */
final class DocumentMapper implements DocumentMapperInterface
{
    private DocumentMapperInterface $nativeDocumentMapper;
    private ContentSubdocumentMapper $contentSubdocumentMapper;
    private ContentTranslationSubdocumentMapper $contentTranslationSubdocumentMapper;

    public function __construct(
        DocumentMapperInterface $nativeDocumentMapper,
        ContentSubdocumentMapper $contentSubdocumentMapper,
        ContentTranslationSubdocumentMapper $contentTranslationSubdocumentMapper
    ) {
        $this->nativeDocumentMapper = $nativeDocumentMapper;
        $this->contentSubdocumentMapper = $contentSubdocumentMapper;
        $this->contentTranslationSubdocumentMapper = $contentTranslationSubdocumentMapper;
    }

    public function mapContentBlock(Content $content): array
    {
        $block = $this->nativeDocumentMapper->mapContentBlock($content);
        $this->escapeDocumentIds($block);
        $subdocuments = $this->getContentSubdocuments($content);

        foreach ($block as $contentDocument) {
            $translationSubdocuments = $this->getContentTranslationSubdocuments($content, $contentDocument->languageCode);

            /* @noinspection SlowArrayOperationsInLoopInspection */
            $contentDocument->documents = array_merge(
                $contentDocument->documents,
                $subdocuments,
                $translationSubdocuments,
            );
        }

        return $block;
    }

    public function generateContentDocumentId($contentId, $languageCode = null): string
    {
        return $this->nativeDocumentMapper->generateContentDocumentId($contentId, $languageCode);
    }

    public function generateLocationDocumentId($locationId, $languageCode = null): string
    {
        return $this->nativeDocumentMapper->generateLocationDocumentId($locationId, $languageCode);
    }

    /**
     * @param \Ibexa\Contracts\Core\Search\Document[] $documents
     */
    private function escapeDocumentIds(array $documents): void
    {
        foreach ($documents as $document) {
            $document->id = preg_replace('([^A-Za-z0-9/]+)', '', $document->id);

            $this->escapeDocumentIds($document->documents);
        }
    }

    /**
     * @param \Ibexa\Contracts\Core\Persistence\Content $content
     *
     * @return array|\Ibexa\Contracts\Core\Search\Document[]
     */
    private function getContentSubdocuments(Content $content): array
    {
        if ($this->contentSubdocumentMapper->accept($content)) {
            return $this->contentSubdocumentMapper->mapDocuments($content);
        }

        return [];
    }

    /**
     * @param \Ibexa\Contracts\Core\Persistence\Content $content
     * @param string $languageCode
     *
     * @return array|\Ibexa\Contracts\Core\Search\Document[]
     */
    private function getContentTranslationSubdocuments(Content $content, string $languageCode): array
    {
        if ($this->contentTranslationSubdocumentMapper->accept($content, $languageCode)) {
            return $this->contentTranslationSubdocumentMapper->mapDocuments($content, $languageCode);
        }

        return [];
    }
}
