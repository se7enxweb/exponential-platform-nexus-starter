<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Tests\Integration\Implementation\Solr\FieldMapper;

use Ibexa\Contracts\Core\Persistence\Content as SPIContent;
use Ibexa\Contracts\Solr\FieldMapper\ContentFieldMapper;

class TestContentFieldMapper extends ContentFieldMapper implements TestFieldMapperInterface
{
    use TestFieldMapperTrait;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function accept(SPIContent $content): bool
    {
        return $this->accepts($content);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function mapFields(SPIContent $content): array
    {
        return $this->getFields($content);
    }
}
