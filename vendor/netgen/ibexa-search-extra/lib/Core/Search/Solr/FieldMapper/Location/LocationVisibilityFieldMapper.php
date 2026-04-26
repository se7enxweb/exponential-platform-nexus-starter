<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Solr\FieldMapper\Location;

use Ibexa\Contracts\Core\Persistence\Content\Handler as ContentHandler;
use Ibexa\Contracts\Core\Persistence\Content\Location as SPILocation;
use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Contracts\Core\Search\FieldType;
use Ibexa\Contracts\Solr\FieldMapper\LocationFieldMapper;

class LocationVisibilityFieldMapper extends LocationFieldMapper
{
    protected ContentHandler $contentHandler;

    public function __construct(ContentHandler $contentHandler)
    {
        $this->contentHandler = $contentHandler;
    }

    public function accept(SPILocation $location): bool
    {
        return true;
    }

    public function mapFields(SPILocation $location): array
    {
        $contentInfo = $this->contentHandler->loadContentInfo($location->contentId);

        return [
            new Field(
                'ng_location_visible',
                !$location->hidden && !$location->invisible && !$contentInfo->isHidden,
                new FieldType\BooleanField(),
            ),
        ];
    }
}
