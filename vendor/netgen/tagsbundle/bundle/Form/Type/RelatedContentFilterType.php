<?php

declare(strict_types=1);

namespace Netgen\TagsBundle\Form\Type;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\ContentTypeTermAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\TermAggregationResult;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;
use Netgen\TagsBundle\Core\Repository\RelatedContentFacetsLoader;
use Netgen\TagsBundle\Core\Search\RelatedContent\SortClauseMapper;
use Netgen\TagsBundle\Exception\FacetingNotSupportedException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RelatedContentFilterType extends AbstractType
{
    public function __construct(
        private RelatedContentFacetsLoader $relatedContentFacetsLoader,
        private ContentTypeService $contentTypeService,
        private SortClauseMapper $sortClauseMapper,
    ) {}

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired('tag')
            ->setAllowedTypes('tag', Tag::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'content_types',
                ChoiceType::class,
                [
                    'choices' => $this->getContentTypeOptions($options['tag']),
                    'label' => 'tag.related_content.filter.content_type',
                    'expanded' => false,
                    'multiple' => true,
                    'required' => false,
                ],
            )->add(
                'sort',
                ChoiceType::class,
                [
                    'choices' => $this->getSortOptions(),
                    'label' => 'tag.related_content.filter.sort',
                    'expanded' => false,
                    'multiple' => false,
                    'required' => true,
                ],
            );
    }

    /**
     * Extracts content type options from facets.
     */
    private function getContentTypeOptions(Tag $tag): array
    {
        try {
            return $this->getContentTypeOptionsFromFacets($tag);
        } catch (FacetingNotSupportedException) {
            // Do nothing
        }

        return $this->getAllContentTypeOptions();
    }

    /**
     * Extracts options for content type filter form select from facets.
     *
     * @throws \Netgen\TagsBundle\Exception\FacetingNotSupportedException
     */
    private function getContentTypeOptionsFromFacets(Tag $tag): array
    {
        $aggregations = [
            new ContentTypeTermAggregation('content_type'),
        ];

        $aggregationResults = $this->relatedContentFacetsLoader->getRelatedContentFacets($tag, $aggregations);

        $options = [];
        foreach ($aggregationResults as $aggregationResult) {
            if (!$aggregationResult instanceof TermAggregationResult) {
                continue;
            }

            foreach ($aggregationResult->getEntries() as $entry) {
                $contentType = $entry->getKey();
                if (!$contentType instanceof ContentType) {
                    continue;
                }

                $value = $contentType->getName() . ' (' . $entry->getCount() . ')';

                $options[$value] = $contentType->identifier;
            }
        }

        return $options;
    }

    /**
     * Get all content type options grouped by content type groups.
     */
    private function getAllContentTypeOptions(): array
    {
        $groups = $this->contentTypeService->loadContentTypeGroups();
        $options = [];

        foreach ($groups as $group) {
            $contentTypes = $this->contentTypeService->loadContentTypes($group);
            $groupOptions = [];

            foreach ($contentTypes as $contentType) {
                $groupOptions[$contentType->getName() ?? ''] = $contentType->identifier;
            }

            $options[$group->identifier] = $groupOptions;
        }

        return $options;
    }

    /**
     * Prepares sort options for form.
     */
    private function getSortOptions(): array
    {
        $sortOptions = $this->sortClauseMapper->getSortOptions();

        $options = [];
        foreach ($sortOptions as $sortOption) {
            $label = 'tag.related_content.filter.sort.' . $sortOption;
            $options[$label] = $sortOption;
        }

        return $options;
    }
}
