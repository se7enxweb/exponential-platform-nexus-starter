<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\RelationListQuery\Handler;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query as IbexaQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Core\FieldType\RelationList\Value as RelationListValue;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\Layouts\Ibexa\ContentProvider\ContentProviderInterface;
use Netgen\Layouts\Ibexa\Parameters\ParameterType as IbexaParameterType;
use Netgen\Layouts\Ibexa\RelationListQuery\Handler\Traits\SelectedContentTrait;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use Netgen\Layouts\Parameters\ParameterType;

use function array_flip;
use function array_map;
use function array_slice;
use function count;
use function is_array;
use function usort;

/**
 * Query handler implementation providing values through Ibexa CMS relation list field.
 */
final class RelationListQueryHandler implements QueryTypeHandlerInterface
{
    use SelectedContentTrait;

    /**
     * @var array<array-key, class-string<\Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause>>
     */
    private static array $sortClauses = [
        'default' => SortClause\DatePublished::class,
        'date_published' => SortClause\DatePublished::class,
        'date_modified' => SortClause\DateModified::class,
        'content_name' => SortClause\ContentName::class,
        'location_priority' => SortClause\Location\Priority::class,
        Location::SORT_FIELD_PATH => SortClause\Location\Path::class,
        Location::SORT_FIELD_PUBLISHED => SortClause\DatePublished::class,
        Location::SORT_FIELD_MODIFIED => SortClause\DateModified::class,
        Location::SORT_FIELD_SECTION => SortClause\SectionIdentifier::class,
        Location::SORT_FIELD_DEPTH => SortClause\Location\Depth::class,
        Location::SORT_FIELD_PRIORITY => SortClause\Location\Priority::class,
        Location::SORT_FIELD_NAME => SortClause\ContentName::class,
        Location::SORT_FIELD_NODE_ID => SortClause\Location\Id::class,
        Location::SORT_FIELD_CONTENTOBJECT_ID => SortClause\ContentId::class,
    ];

    public function __construct(
        LocationService $locationService,
        ContentProviderInterface $contentProvider,
        private SearchService $searchService,
    ) {
        $this->locationService = $locationService;
        $this->contentProvider = $contentProvider;
    }

    public function buildParameters(ParameterBuilderInterface $builder): void
    {
        $advancedGroup = [self::GROUP_ADVANCED];

        $builder->add(
            'use_current_location',
            ParameterType\Compound\BooleanType::class,
            [
                'reverse' => true,
            ],
        );

        $builder->get('use_current_location')->add(
            'location_id',
            IbexaParameterType\LocationType::class,
            [
                'allow_invalid' => true,
            ],
        );

        $builder->add(
            'field_definition_identifier',
            ParameterType\TextLineType::class,
            [
                'required' => true,
            ],
        );

        $builder->add(
            'sort_type',
            ParameterType\ChoiceType::class,
            [
                'required' => true,
                'options' => [
                    'Defined by field' => 'defined_by_field',
                    'Published' => 'date_published',
                    'Modified' => 'date_modified',
                    'Alphabetical' => 'content_name',
                ],
            ],
        );

        $builder->add(
            'sort_direction',
            ParameterType\ChoiceType::class,
            [
                'required' => true,
                'options' => [
                    'Descending' => IbexaQuery::SORT_DESC,
                    'Ascending' => IbexaQuery::SORT_ASC,
                ],
            ],
        );

        $builder->add(
            'only_main_locations',
            ParameterType\BooleanType::class,
            [
                'default_value' => true,
                'groups' => $advancedGroup,
            ],
        );

        $builder->add(
            'filter_by_content_type',
            ParameterType\Compound\BooleanType::class,
            [
                'groups' => $advancedGroup,
            ],
        );

        $builder->get('filter_by_content_type')->add(
            'content_types',
            IbexaParameterType\ContentTypeType::class,
            [
                'multiple' => true,
                'groups' => $advancedGroup,
            ],
        );

        $builder->get('filter_by_content_type')->add(
            'content_types_filter',
            ParameterType\ChoiceType::class,
            [
                'required' => true,
                'options' => [
                    'Include content types' => 'include',
                    'Exclude content types' => 'exclude',
                ],
                'groups' => $advancedGroup,
            ],
        );
    }

    public function getValues(Query $query, int $offset = 0, ?int $limit = null): iterable
    {
        $relatedContentIds = $this->getRelatedContentIds($query);
        $sortType = $query->getParameter('sort_type')->value ?? 'default';
        $sortDirection = $query->getParameter('sort_direction')->value ?? IbexaQuery::SORT_DESC;

        if (count($relatedContentIds) === 0) {
            return [];
        }

        $locationQuery = $this->buildLocationQuery($relatedContentIds, $query, false, $offset, $limit);

        // We're disabling query count for performance reasons, however
        // it can only be disabled if limit is not 0
        $locationQuery->performCount = $locationQuery->limit === 0;

        $searchResult = $this->searchService->findLocations($locationQuery);

        $locations = array_map(
            static fn (SearchHit $searchHit): Location => $searchHit->valueObject,
            $searchResult->searchHits,
        );

        if ($sortType === 'defined_by_field') {
            $this->sortLocationsByField($relatedContentIds, $locations, $sortDirection);
        }

        return $locations;
    }

    public function getCount(Query $query): int
    {
        $relatedContentIds = $this->getRelatedContentIds($query);

        if (count($relatedContentIds) === 0) {
            return 0;
        }

        $searchResult = $this->searchService->findLocations(
            $this->buildLocationQuery($relatedContentIds, $query, true),
        );

        return $searchResult->totalCount ?? 0;
    }

    public function isContextual(Query $query): bool
    {
        return $query->getParameter('use_current_location')->value === true;
    }

    /**
     * Sort given $locations as defined by the given $relatedContentIds.
     *
     * @param int[] $relatedContentIds
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location[] $locations
     */
    private function sortLocationsByField(
        array $relatedContentIds,
        array &$locations,
        string $sortDirection,
    ): void {
        $sortMap = array_flip($relatedContentIds);

        usort(
            $locations,
            static function (Location $location1, Location $location2) use ($sortMap, $sortDirection): int {
                if ($location1->contentId === $location2->contentId) {
                    return 0;
                }

                if ($sortDirection === IbexaQuery::SORT_ASC) {
                    return $sortMap[$location1->contentId] <=> $sortMap[$location2->contentId];
                }

                return $sortMap[$location2->contentId] <=> $sortMap[$location1->contentId];
            },
        );
    }

    /**
     * Returns a list of related Content IDs defined in the given collection $query.
     *
     * @return int[]
     */
    private function getRelatedContentIds(Query $query): array
    {
        $content = $this->getSelectedContent($query);

        if ($content === null) {
            return [];
        }

        $field = $content->getField($query->getParameter('field_definition_identifier')->value);
        if ($field === null || !$field->value instanceof RelationListValue) {
            return [];
        }

        return $field->value->destinationContentIds;
    }

    /**
     * Builds the Location query from given parameters.
     *
     * @param int[] $relatedContentIds
     */
    private function buildLocationQuery(
        array $relatedContentIds,
        Query $query,
        bool $buildCountQuery = false,
        int $offset = 0,
        ?int $limit = null,
    ): LocationQuery {
        $locationQuery = new LocationQuery();
        $sortType = $query->getParameter('sort_type')->value ?? 'default';
        $sortDirection = $query->getParameter('sort_direction')->value ?? IbexaQuery::SORT_DESC;

        if ($sortType === 'defined_by_field') {
            $relatedContentIds = array_slice($relatedContentIds, $offset, $limit);
        }

        $criteria = [
            new Criterion\ContentId($relatedContentIds),
            new Criterion\Visibility(Criterion\Visibility::VISIBLE),
        ];

        if ($query->getParameter('only_main_locations')->value === true) {
            $criteria[] = new Criterion\Location\IsMainLocation(
                Criterion\Location\IsMainLocation::MAIN,
            );
        }

        if ($query->getParameter('filter_by_content_type')->value === true) {
            /** @var string[]|null $contentTypes */
            $contentTypes = $query->getParameter('content_types')->value;
            if (is_array($contentTypes) && count($contentTypes) > 0) {
                $contentTypeFilter = new Criterion\ContentTypeIdentifier($contentTypes);

                if ($query->getParameter('content_types_filter')->value === 'exclude') {
                    $contentTypeFilter = new Criterion\LogicalNot($contentTypeFilter);
                }

                $criteria[] = $contentTypeFilter;
            }
        }

        $locationQuery->filter = new Criterion\LogicalAnd($criteria);

        $locationQuery->limit = 0;
        if (!$buildCountQuery) {
            $locationQuery->offset = $offset;
            if ($limit !== null) {
                $locationQuery->limit = $limit;
            }
        }

        if ($sortType !== 'defined_by_field') {
            $sortClause = new self::$sortClauses[$sortType]($sortDirection);
            $locationQuery->sortClauses = [$sortClause];
        }

        return $locationQuery;
    }
}
