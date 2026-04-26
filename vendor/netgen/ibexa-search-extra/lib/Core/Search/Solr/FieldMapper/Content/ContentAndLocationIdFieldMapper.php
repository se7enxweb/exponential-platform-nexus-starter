<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Solr\FieldMapper\Content;

use Ibexa\Contracts\Core\Persistence\Content as SPIContent;
use Ibexa\Contracts\Core\Persistence\Content\Location\Handler as LocationHandler;
use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Contracts\Core\Search\FieldType\IntegerField;
use Ibexa\Contracts\Core\Search\FieldType\MultipleIntegerField;
use Ibexa\Contracts\Solr\FieldMapper\ContentFieldMapper;

class ContentAndLocationIdFieldMapper extends ContentFieldMapper
{
    protected LocationHandler $locationHandler;

    public function __construct(LocationHandler $locationHandler)
    {
        $this->locationHandler = $locationHandler;
    }

    public function accept(SPIContent $content): bool
    {
        return true;
    }

    public function mapFields(SPIContent $content): array
    {
        $locations = $this->locationHandler->loadLocationsByContent($content->versionInfo->contentInfo->id);
        $locationIds = [];

        foreach ($locations as $location) {
            $locationIds[] = $location->id;
        }

        return [
            new Field(
                'ng_content_id',
                $content->versionInfo->contentInfo->id,
                new IntegerField(),
            ),
            new Field(
                'ng_location_id',
                $locationIds,
                new MultipleIntegerField(),
            ),
        ];
    }
}
