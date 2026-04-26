<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\SiteApi\Tests\Stubs;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Core\FieldType\Null\Value as NullValue;
use Netgen\IbexaSiteApi\API\Values\Content as APIContent;
use Netgen\IbexaSiteApi\Core\Site\Values\Field;
use Pagerfanta\Pagerfanta;

final class Content extends APIContent
{
    protected int $id;

    protected ContentInfo $contentInfo;

    public function hasField(string $identifier): false
    {
        return false;
    }

    public function getField(string $identifier): Field
    {
        return new Field();
    }

    public function hasFieldById(int $id): false
    {
        return false;
    }

    public function getFieldById(int $id): Field
    {
        return new Field();
    }

    public function getFirstNonEmptyField(string $firstIdentifier, string ...$otherIdentifiers): Field
    {
        return new Field();
    }

    public function getFieldValue(string $identifier): Value
    {
        return new NullValue();
    }

    public function getFieldValueById(int $id): Value
    {
        return new NullValue();
    }

    public function getLocations(int $limit = 25): array
    {
        return [];
    }

    /**
     * @return \Pagerfanta\Pagerfanta<\Netgen\IbexaSiteApi\API\Values\Location>
     */
    public function filterLocations(int $maxPerPage = 25, int $currentPage = 1): Pagerfanta
    {
        return new Pagerfanta(new LocationAdapter());
    }

    public function getFieldRelation(string $fieldDefinitionIdentifier): self
    {
        return new self();
    }

    public function getFieldRelations(string $fieldDefinitionIdentifier, int $limit = 25): array
    {
        return [];
    }

    /**
     * @return \Pagerfanta\Pagerfanta<\Netgen\IbexaSiteApi\API\Values\Content>
     */
    public function filterFieldRelations(
        string $fieldDefinitionIdentifier,
        array $contentTypeIdentifiers = [],
        int $maxPerPage = 25,
        int $currentPage = 1,
    ): Pagerfanta {
        return new Pagerfanta(new ContentAdapter());
    }

    public function getFieldRelationLocation(string $fieldDefinitionIdentifier): null
    {
        return null;
    }

    public function getFieldRelationLocations(string $fieldDefinitionIdentifier, int $limit = 25): array
    {
        return [];
    }

    /**
     * @return \Pagerfanta\Pagerfanta<\Netgen\IbexaSiteApi\API\Values\Location>
     */
    public function filterFieldRelationLocations(
        string $fieldDefinitionIdentifier,
        array $contentTypeIdentifiers = [],
        int $maxPerPage = 25,
        int $currentPage = 1,
    ): Pagerfanta {
        return new Pagerfanta(new LocationAdapter());
    }

    public function getSudoFieldRelation(string $fieldDefinitionIdentifier): self
    {
        return new self();
    }

    public function getSudoFieldRelations(string $fieldDefinitionIdentifier, int $limit = 25): array
    {
        return [];
    }

    /**
     * @return \Pagerfanta\Pagerfanta<\Netgen\IbexaSiteApi\API\Values\Content>
     */
    public function filterSudoFieldRelations(
        string $fieldDefinitionIdentifier,
        array $contentTypeIdentifiers = [],
        int $maxPerPage = 25,
        int $currentPage = 1,
    ): Pagerfanta {
        return new Pagerfanta(new ContentAdapter());
    }

    public function getSudoFieldRelationLocation(string $fieldDefinitionIdentifier): null
    {
        return null;
    }

    public function getSudoFieldRelationLocations(string $fieldDefinitionIdentifier, int $limit = 25): array
    {
        return [];
    }

    /**
     * @return \Pagerfanta\Pagerfanta<\Netgen\IbexaSiteApi\API\Values\Location>
     */
    public function filterSudoFieldRelationLocations(
        string $fieldDefinitionIdentifier,
        array $contentTypeIdentifiers = [],
        int $maxPerPage = 25,
        int $currentPage = 1,
    ): Pagerfanta {
        return new Pagerfanta(new LocationAdapter());
    }

    public function getReverseFieldRelations(string $fieldDefinitionIdentifier, int $limit = 25): array
    {
        return [];
    }

    public function getSudoReverseFieldRelations(string $fieldDefinitionIdentifier, int $limit = 25): array
    {
        return [];
    }

    /**
     * @return \Pagerfanta\Pagerfanta<\Netgen\IbexaSiteApi\API\Values\Content>
     */
    public function filterReverseFieldRelations(
        string $fieldDefinitionIdentifier,
        array $contentTypeIdentifiers = [],
        int $maxPerPage = 25,
        int $currentPage = 1,
    ): Pagerfanta {
        return new Pagerfanta(new ContentAdapter());
    }

    /**
     * @return \Pagerfanta\Pagerfanta<\Netgen\IbexaSiteApi\API\Values\Content>
     */
    public function filterSudoReverseFieldRelations(
        string $fieldDefinitionIdentifier,
        array $contentTypeIdentifiers = [],
        int $maxPerPage = 25,
        int $currentPage = 1,
    ): Pagerfanta {
        return new Pagerfanta(new ContentAdapter());
    }

    public function getReverseFieldRelationLocations(string $fieldDefinitionIdentifier, int $limit = 25): array
    {
        return [];
    }

    public function getSudoReverseFieldRelationLocations(string $fieldDefinitionIdentifier, int $limit = 25): array
    {
        return [];
    }

    /**
     * @return \Pagerfanta\Pagerfanta<\Netgen\IbexaSiteApi\API\Values\Location>
     */
    public function filterReverseFieldRelationLocations(
        string $fieldDefinitionIdentifier,
        array $contentTypeIdentifiers = [],
        int $maxPerPage = 25,
        int $currentPage = 1,
    ): Pagerfanta {
        return new Pagerfanta(new LocationAdapter());
    }

    /**
     * @return \Pagerfanta\Pagerfanta<\Netgen\IbexaSiteApi\API\Values\Location>
     */
    public function filterSudoReverseFieldRelationLocations(
        string $fieldDefinitionIdentifier,
        array $contentTypeIdentifiers = [],
        int $maxPerPage = 25,
        int $currentPage = 1,
    ): Pagerfanta {
        return new Pagerfanta(new LocationAdapter());
    }

    /**
     * @param mixed[] $parameters
     */
    public function getPath(array $parameters = []): string
    {
        return '/example';
    }

    /**
     * @param mixed[] $parameters
     */
    public function getUrl(array $parameters = []): string
    {
        return 'https://netgen.io/example';
    }

    public function getDebugInfo(): array
    {
        return [];
    }
}
