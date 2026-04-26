<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\TagsQuery\Handler;

use Ibexa\Contracts\Core\Persistence\Content\ObjectState\Handler as ObjectStateHandler;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\Layouts\Ibexa\Collection\QueryType\Handler\Traits;
use Netgen\Layouts\Ibexa\ContentProvider\ContentProviderInterface;
use Netgen\Layouts\Ibexa\Parameters\ParameterType as IbexaParameterType;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use Netgen\Layouts\Parameters\ParameterType;
use Netgen\TagsBundle\API\Repository\Values\Content\Query\Criterion\TagId;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;
use Netgen\TagsBundle\Core\FieldType\Tags\Value as TagsFieldValue;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

use function array_filter;
use function array_map;
use function array_merge;
use function array_unique;
use function array_values;
use function count;
use function explode;
use function is_int;
use function is_string;
use function max;

/**
 * Query handler implementation providing values through Ibexa CMS Tags field.
 */
final class TagsQueryHandler implements QueryTypeHandlerInterface
{
    use Traits\ContentTypeFilterTrait;
    use Traits\CurrentLocationFilterTrait;
    use Traits\MainLocationFilterTrait;
    use Traits\ObjectStateFilterTrait;
    use Traits\ParentLocationTrait;
    use Traits\QueryTypeFilterTrait;
    use Traits\SortTrait;

    public function __construct(
        LocationService $locationService,
        ObjectStateHandler $objectStateHandler,
        ContentProviderInterface $contentProvider,
        private SearchService $searchService,
        private RequestStack $requestStack,
    ) {
        $this->locationService = $locationService;
        $this->objectStateHandler = $objectStateHandler;
        $this->contentProvider = $contentProvider;
    }

    public function buildParameters(ParameterBuilderInterface $builder): void
    {
        $advancedGroup = [self::GROUP_ADVANCED];

        $this->buildParentLocationParameters($builder);

        $builder->add(
            'filter_by_tags',
            IbexaParameterType\TagsType::class,
            [
                'allow_invalid' => true,
            ],
        );

        $builder->add(
            'use_tags_from_current_content',
            ParameterType\Compound\BooleanType::class,
            [
                'groups' => $advancedGroup,
            ],
        );

        $builder->get('use_tags_from_current_content')->add(
            'field_definition_identifier',
            ParameterType\TextLineType::class,
            [
                'groups' => $advancedGroup,
            ],
        );

        $builder->add(
            'use_tags_from_query_string',
            ParameterType\Compound\BooleanType::class,
            [
                'groups' => $advancedGroup,
            ],
        );

        $builder->get('use_tags_from_query_string')->add(
            'query_string_param_name',
            ParameterType\TextLineType::class,
            [
                'groups' => $advancedGroup,
            ],
        );

        $builder->add(
            'tags_filter_logic',
            ParameterType\ChoiceType::class,
            [
                'required' => true,
                'options' => [
                    'Match any tags' => 'any',
                    'Match all tags' => 'all',
                ],
                'groups' => $advancedGroup,
            ],
        );

        $this->buildSortParameters($builder, [], ['date_published', 'date_modified', 'content_name']);

        $this->buildQueryTypeParameters($builder, $advancedGroup);
        $this->buildMainLocationParameters($builder, $advancedGroup);
        $this->buildContentTypeFilterParameters($builder, $advancedGroup);
        $this->buildCurrentLocationParameters($builder, $advancedGroup);
        $this->buildObjectStateFilterParameters($builder, $advancedGroup);
    }

    public function getValues(Query $query, int $offset = 0, ?int $limit = null): iterable
    {
        $parentLocation = $this->getParentLocation($query);

        if (!$parentLocation instanceof Location) {
            return [];
        }

        $tagIds = $this->getTagIds($query);

        if (count($tagIds) === 0) {
            return [];
        }

        $locationQuery = $this->buildLocationQuery($query, $parentLocation, $tagIds);
        $locationQuery->offset = $this->getOffset($offset);
        $locationQuery->limit = $this->getLimit($limit) ?? $locationQuery->limit;

        // We're disabling query count for performance reasons, however
        // it can only be disabled if limit is not 0
        $locationQuery->performCount = $locationQuery->limit === 0;

        $searchResult = $this->searchService->findLocations($locationQuery);

        return array_map(
            static fn (SearchHit $searchHit): Location => $searchHit->valueObject,
            $searchResult->searchHits,
        );
    }

    public function getCount(Query $query): int
    {
        $parentLocation = $this->getParentLocation($query);

        if (!$parentLocation instanceof Location) {
            return 0;
        }

        $tagIds = $this->getTagIds($query);

        if (count($tagIds) === 0) {
            return 0;
        }

        $locationQuery = $this->buildLocationQuery($query, $parentLocation, $tagIds);
        $locationQuery->limit = 0;

        $searchResult = $this->searchService->findLocations($locationQuery);

        return $searchResult->totalCount ?? 0;
    }

    public function isContextual(Query $query): bool
    {
        return $query->getParameter('use_current_location')->value === true
            || $query->getParameter('use_tags_from_current_content')->value === true;
    }

    /**
     * Return filtered offset value to use.
     */
    private function getOffset(int $offset): int
    {
        return max($offset, 0);
    }

    /**
     * Return filtered limit value to use.
     */
    private function getLimit(?int $limit = null): ?int
    {
        if (is_int($limit) && $limit >= 0) {
            return $limit;
        }

        return null;
    }

    /**
     * Returns a list of tag IDs.
     *
     * @return int[]
     */
    private function getTagIds(Query $query): array
    {
        $tags = [];

        if (!$query->getParameter('filter_by_tags')->isEmpty) {
            $tags[] = array_values($query->getParameter('filter_by_tags')->value);
        }

        if ($query->getParameter('use_tags_from_current_content')->value === true) {
            $tags[] = $this->getTagsFromContent($query);
        }

        $request = $this->requestStack->getCurrentRequest();
        if ($request instanceof Request && $query->getParameter('use_tags_from_query_string')->value === true) {
            $queryStringParam = $query->getParameter('query_string_param_name');

            if (!$queryStringParam->isEmpty && is_string($queryStringParam->value)) {
                if ($request->query->has($queryStringParam->value)) {
                    $tags[] = $request->query->all($queryStringParam->value);
                }
            }
        }

        return array_map(intval(...), array_values(array_unique(array_merge(...$tags))));
    }

    /**
     * Builds the Location query from given parameters.
     *
     * @param int[] $tagIds
     */
    private function buildLocationQuery(Query $query, Location $parentLocation, array $tagIds): LocationQuery
    {
        $tagsCriteria = array_map(static fn (int $tagId): TagId => new TagId($tagId), $tagIds);

        $criteria = [
            new Criterion\Subtree($parentLocation->pathString),
            new Criterion\Visibility(Criterion\Visibility::VISIBLE),
            $this->getQueryTypeFilterCriteria($query, $parentLocation),
            $query->getParameter('tags_filter_logic')->value === 'any' ?
                new Criterion\LogicalOr($tagsCriteria) :
                new Criterion\LogicalAnd($tagsCriteria),
            $this->getMainLocationFilterCriteria($query),
            $this->getContentTypeFilterCriteria($query),
            $this->getObjectStateFilterCriteria($query),
        ];

        $currentLocation = $this->contentProvider->provideLocation();
        if ($currentLocation instanceof Location) {
            $criteria[] = $this->getCurrentLocationFilterCriteria($query, $currentLocation);
        }

        $criteria = array_filter(
            $criteria,
            static fn (?CriterionInterface $criterion): bool => $criterion instanceof CriterionInterface,
        );

        $locationQuery = new LocationQuery();
        $locationQuery->filter = new Criterion\LogicalAnd($criteria);
        $locationQuery->sortClauses = $this->getSortClauses($query, $parentLocation);

        return $locationQuery;
    }

    /**
     * @return iterable<string>
     */
    private function getValidFieldIdentifiers(Query $query, Content $content): iterable
    {
        $parameter = $query->getParameter('field_definition_identifier');

        if (!$parameter->isEmpty) {
            return array_map(mb_trim(...), explode(',', $parameter->value));
        }

        foreach ($content->getFields() as $field) {
            yield $field->fieldDefIdentifier;
        }
    }

    /**
     * @return int[]
     */
    private function getTagsFromContent(Query $query): array
    {
        $content = $this->contentProvider->provideContent();

        if ($content === null) {
            return [];
        }

        /** @var string[] $fieldIdentifiers */
        $fieldIdentifiers = [...$this->getValidFieldIdentifiers($query, $content)];

        $tags = [];

        foreach ($fieldIdentifiers as $fieldIdentifier) {
            $tags[] = $this->getTagsFromField($content, $fieldIdentifier);
        }

        return array_merge(...$tags);
    }

    /**
     * @return int[]
     */
    private function getTagsFromField(Content $content, string $fieldDefinitionIdentifier): array
    {
        $field = $content->getField($fieldDefinitionIdentifier);
        if ($field === null || !$field->value instanceof TagsFieldValue) {
            return [];
        }

        return array_map(
            static fn (Tag $tag): int => $tag->id,
            $field->value->tags,
        );
    }
}
