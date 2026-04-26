<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\RelationListQuery\Handler;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query as IbexaQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\Layouts\Ibexa\ContentProvider\ContentProviderInterface;
use Netgen\Layouts\Ibexa\Parameters\ParameterType as IbexaParameterType;
use Netgen\Layouts\Ibexa\RelationListQuery\Handler\Traits\SelectedContentTrait;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use Netgen\Layouts\Parameters\ParameterType;

use function count;
use function is_array;

/**
 * Query handler implementation providing values through Ibexa CMS reverse relation.
 */
final class ReverseRelationListQueryHandler implements QueryTypeHandlerInterface
{
    use SelectedContentTrait;

    /**
     * @var array<string, class-string<\Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause>>
     */
    private static array $sortClauses = [
        'default' => SortClause\DatePublished::class,
        'date_published' => SortClause\DatePublished::class,
        'date_modified' => SortClause\DateModified::class,
        'content_name' => SortClause\ContentName::class,
    ];

    public function __construct(
        LocationService $locationService,
        ContentProviderInterface $contentProvider,
        private ContentService $contentService,
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
            'sort_type',
            ParameterType\ChoiceType::class,
            [
                'required' => true,
                'options' => [
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

        $builder->get('filter_by_content_type')->add(
            'field_definition_identifier',
            ParameterType\TextLineType::class,
            [
                'required' => false,
                'groups' => $advancedGroup,
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
    }

    public function getValues(Query $query, int $offset = 0, ?int $limit = null): iterable
    {
        $reverseRelatedContentIds = $this->getReverseRelatedContentIds($query);

        if (count($reverseRelatedContentIds) === 0) {
            return [];
        }

        $locationQuery = $this->buildLocationQuery($reverseRelatedContentIds, $query, false, $offset, $limit);

        // We're disabling query count for performance reasons, however
        // it can only be disabled if limit is not 0
        $locationQuery->performCount = $locationQuery->limit === 0;

        $searchResult = $this->searchService->findLocations($locationQuery);

        foreach ($searchResult->searchHits as $searchHit) {
            yield $searchHit->valueObject;
        }
    }

    public function getCount(Query $query): int
    {
        $reverseRelatedContentIds = $this->getReverseRelatedContentIds($query);

        if (count($reverseRelatedContentIds) === 0) {
            return 0;
        }

        $searchResult = $this->searchService->findLocations(
            $this->buildLocationQuery($reverseRelatedContentIds, $query, true),
        );

        return $searchResult->totalCount ?? 0;
    }

    public function isContextual(Query $query): bool
    {
        return $query->getParameter('use_current_location')->value === true;
    }

    /**
     * Returns a list Content IDs whose content relates to selected content.
     *
     * @return int[]
     */
    private function getReverseRelatedContentIds(Query $query): array
    {
        $content = $this->getSelectedContent($query);

        if ($content === null) {
            return [];
        }

        $contentIds = [];

        foreach ($this->contentService->loadReverseRelations($content->contentInfo) as $relation) {
            $contentIds[] = $relation->getSourceContentInfo()->id;
        }

        return $contentIds;
    }

    /**
     * Builds the Location query from given parameters.
     *
     * @param int[] $reverseRelatedContentIds
     */
    private function buildLocationQuery(
        array $reverseRelatedContentIds,
        Query $query,
        bool $buildCountQuery = false,
        int $offset = 0,
        ?int $limit = null,
    ): LocationQuery {
        $locationQuery = new LocationQuery();
        $sortType = $query->getParameter('sort_type')->value ?? 'default';
        $sortDirection = $query->getParameter('sort_direction')->value ?? IbexaQuery::SORT_DESC;

        $criteria = [
            new Criterion\ContentId($reverseRelatedContentIds),
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

            $fieldDefinitionIdentifier = $query->getParameter('field_definition_identifier')->value;
            $selectedContent = $this->getSelectedContent($query);

            if ($fieldDefinitionIdentifier !== null && $selectedContent !== null) {
                $criteria[] = new Criterion\Field(
                    $fieldDefinitionIdentifier,
                    Criterion\Operator::CONTAINS,
                    $selectedContent->id,
                );
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

        $sortClause = new self::$sortClauses[$sortType]($sortDirection);
        $locationQuery->sortClauses = [$sortClause];

        return $locationQuery;
    }
}
