<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Elasticsearch\DocumentMapper;

use ArrayIterator;
use Ibexa\Contracts\Core\Persistence\Content;
use Ibexa\Contracts\Core\Persistence\Content\Handler as ContentHandler;
use Ibexa\Contracts\Core\Persistence\Content\Location;
use Ibexa\Elasticsearch\DocumentMapper\DocumentFactoryInterface;
use Iterator;

use function iterator_to_array;

class DocumentFactory implements DocumentFactoryInterface
{
    public function __construct(
        private readonly DocumentFactoryInterface $innerDocumentFactory,
        private readonly ContentHandler $contentHandler,
        private readonly ContentFieldMapper $contentFieldMapper,
        private readonly LocationFieldMapper $locationFieldMapper,
        private readonly ContentTranslationFieldMapper $contentTranslationFieldMapper,
        private readonly LocationTranslationFieldMapper $locationTranslationFieldMapper,
        private readonly BlockFieldMapper $blockFieldMapper,
        private readonly BlockTranslationFieldMapper $blockTranslationMapper,
    ) {}

    public function fromContent(Content $content): Iterator
    {
        $result = $this->innerDocumentFactory->fromContent($content);

        /** @var \Ibexa\Contracts\Elasticsearch\Mapping\ContentDocument[] $documents */
        $documents = iterator_to_array($result);

        foreach ($documents as $document) {
            $contentFields = $this->contentFieldMapper->mapFields($content);
            $contentTranslationDependentFields = $this->contentTranslationFieldMapper->mapFields($content, $document->languageCode);
            $blockFields = $this->blockFieldMapper->mapFields($content);
            $blockTranslationDependentFields = $this->blockTranslationMapper->mapFields($content, $document->languageCode);

            $document->fields = [
                ...$document->fields,
                ...$contentFields,
                ...$contentTranslationDependentFields,
                ...$blockFields,
                ...$blockTranslationDependentFields,
            ];
        }

        return new ArrayIterator($documents);
    }

    public function fromLocation(Location $location, ?Content $content = null): Iterator
    {
        if ($content === null) {
            $content = $this->contentHandler->load($location->contentId);
        }
        $result = $this->innerDocumentFactory->fromLocation($location, $content);

        /** @var \Ibexa\Contracts\Elasticsearch\Mapping\LocationDocument[] $documents */
        $documents = iterator_to_array($result);

        foreach ($documents as $document) {
            $locationFields = $this->locationFieldMapper->mapFields($location);
            $locationTranslationDependentFields = $this->locationTranslationFieldMapper->mapFields($location, $document->languageCode);
            $blockFields = $this->blockFieldMapper->mapFields($content);
            $blockTranslationDependentFields = $this->blockTranslationMapper->mapFields($content, $document->languageCode);

            $document->fields = [
                ...$document->fields,
                ...$locationFields,
                ...$locationTranslationDependentFields,
                ...$blockFields,
                ...$blockTranslationDependentFields,
            ];
        }

        return new ArrayIterator($documents);
    }
}
