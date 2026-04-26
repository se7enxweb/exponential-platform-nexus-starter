<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Tests\Integration\Implementation\Solr\FieldMapper;

use Ibexa\Contracts\Core\Persistence\Content;
use Ibexa\Contracts\Core\Persistence\Content as SPIContent;
use Ibexa\Contracts\Core\Persistence\Content\Field as PersistenceField;
use Ibexa\Contracts\Core\Persistence\Content\Handler as ContentHandler;
use Ibexa\Contracts\Core\Persistence\Content\Type as ContentType;
use Ibexa\Contracts\Core\Persistence\Content\Type\Handler as ContentTypeHandler;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Contracts\Core\Search\FieldType\BooleanField;
use Ibexa\Contracts\Core\Search\FieldType\IntegerField;
use Ibexa\Contracts\Core\Search\FieldType\StringField;
use Ibexa\Core\Search\Legacy\Content\Handler as SearchHandler;
use RuntimeException;

trait TestFieldMapperTrait
{
    private ContentHandler $contentHandler;
    private ContentTypeHandler $contentTypeHandler;
    private SearchHandler $searchHandler;

    public function __construct(
        ContentHandler $contentHandler,
        ContentTypeHandler $contentTypeHandler,
        SearchHandler $searchHandler
    ) {
        $this->contentHandler = $contentHandler;
        $this->contentTypeHandler = $contentTypeHandler;
        $this->searchHandler = $searchHandler;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function accepts(SPIContent $content): bool
    {
        $contentType = $this->contentTypeHandler->load(
            $content->versionInfo->contentInfo->contentTypeId,
        );

        return $contentType->identifier === self::CONTENT_TYPE_IDENTIFIER;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     *
     * @return \Ibexa\Contracts\Core\Search\Field[]
     */
    public function getFields(SPIContent $content): array
    {
        $contentType = $this->contentTypeHandler->load(
            $content->versionInfo->contentInfo->contentTypeId,
        );

        $commentCount = $this->getCommentCount($content);

        $prefixedName = 'prefix ' . $this->extractField($content, $contentType, 'name')->value->data;

        return [
            new Field(
                'extra_prefixed_name',
                $prefixedName,
                new StringField(),
            ),
            new Field(
                'extra_comment_count',
                $commentCount,
                new IntegerField(),
            ),
            new Field(
                'extra_has_comments',
                $commentCount > 0,
                new BooleanField(),
            ),
            new Field(
                'extra_content_type_identifier',
                $contentType->identifier,
                new StringField(),
            ),
        ];
    }

    private function extractField(Content $content, ContentType $contentType, string $identifier): PersistenceField
    {
        $fieldDefinitionId = $this->getFieldDefinitionId($contentType, $identifier);

        foreach ($content->fields as $field) {
            if ($field->fieldDefinitionId === $fieldDefinitionId) {
                return $field;
            }
        }

        throw new RuntimeException(
            "Could not extract field '{$identifier}'",
        );
    }

    private function getFieldDefinitionId(ContentType $contentType, string $identifier): int
    {
        foreach ($contentType->fieldDefinitions as $fieldDefinition) {
            if ($fieldDefinition->identifier === $identifier) {
                return $fieldDefinition->id;
            }
        }

        throw new RuntimeException(
            "Could not extract field definition '{$identifier}'",
        );
    }

    private function getCommentCount(Content $content): int
    {
        $criteria = [
            new Criterion\ParentLocationId($content->versionInfo->contentInfo->mainLocationId),
            new Criterion\ContentTypeIdentifier(self::CHILD_CONTENT_TYPE_IDENTIFIER),
        ];

        $query = new LocationQuery();
        $query->filter = new Criterion\LogicalAnd($criteria);
        $query->limit = 0;

        return $this->searchHandler->findLocations($query)->totalCount;
    }
}
