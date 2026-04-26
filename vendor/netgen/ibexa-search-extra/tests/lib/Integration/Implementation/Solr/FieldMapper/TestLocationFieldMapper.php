<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Tests\Integration\Implementation\Solr\FieldMapper;

use Ibexa\Contracts\Core\Persistence\Content\Location as SPILocation;
use Ibexa\Contracts\Solr\FieldMapper\LocationFieldMapper;

class TestLocationFieldMapper extends LocationFieldMapper implements TestFieldMapperInterface
{
    use TestFieldMapperTrait;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function accept(SPILocation $location): bool
    {
        $content = $this->contentHandler->load($location->contentId);

        return $this->accepts($content);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     *
     * @return \Ibexa\Contracts\Core\Search\Field[]
     */
    public function mapFields(SPILocation $location): array
    {
        $content = $this->contentHandler->load($location->contentId);

        return $this->getFields($content);
    }
}
