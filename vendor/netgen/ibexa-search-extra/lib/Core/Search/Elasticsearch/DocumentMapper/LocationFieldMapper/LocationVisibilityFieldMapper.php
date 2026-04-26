<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Elasticsearch\DocumentMapper\LocationFieldMapper;

use Ibexa\Contracts\Core\Persistence\Content\Handler as ContentHandler;
use Ibexa\Contracts\Core\Persistence\Content\Location as SPILocation;
use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Contracts\Core\Search\FieldType\BooleanField;
use Netgen\IbexaSearchExtra\Core\Search\Elasticsearch\DocumentMapper\LocationFieldMapper;

final class LocationVisibilityFieldMapper extends LocationFieldMapper
{
    public function __construct(
        private readonly ContentHandler $contentHandler,
    ) {
    }

    public function accept(SPILocation $location): bool
    {
        return true;
    }

    public function mapFields(SPILocation $location): array
    {
        $content = $this->contentHandler->load($location->contentId);

        return [
            new Field(
                'ng_location_visible',
                !$location->hidden && !$location->invisible && !$content->versionInfo->contentInfo->isHidden,
                new BooleanField(),
            ),
        ];
    }
}
