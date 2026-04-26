<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Solr\FieldMapper\Location;

use Ibexa\Contracts\Core\Persistence\Content\Location as SPILocation;
use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Contracts\Core\Search\FieldType\IntegerField;
use Ibexa\Contracts\Solr\FieldMapper\LocationFieldMapper;

class LocationIdFieldMapper extends LocationFieldMapper
{
    public function accept(SPILocation $location): bool
    {
        return true;
    }

    public function mapFields(SPILocation $location): array
    {
        return [
            new Field(
                'ng_location_id',
                $location->id,
                new IntegerField(),
            ),
        ];
    }
}
