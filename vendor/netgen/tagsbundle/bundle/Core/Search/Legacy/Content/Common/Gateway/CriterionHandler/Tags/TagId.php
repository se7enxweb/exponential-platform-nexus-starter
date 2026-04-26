<?php

declare(strict_types=1);

namespace Netgen\TagsBundle\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\Tags;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Netgen\TagsBundle\API\Repository\Values\Content\Query\Criterion\TagId as TagIdCriterion;
use Netgen\TagsBundle\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\Tags;

final class TagId extends Tags
{
    public function accept(CriterionInterface $criterion): bool
    {
        return $criterion instanceof TagIdCriterion;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion $criterion
     */
    public function handle(CriteriaConverter $converter, QueryBuilder $queryBuilder, CriterionInterface $criterion, array $languageSettings): string
    {
        $subSelect = $this->connection->createQueryBuilder();
        $subSelect
            ->select('t1.id')
            ->from('ibexa_content', 't1')
            ->innerJoin(
                't1',
                'eztags_attribute_link',
                't2',
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->eq('t2.objectattribute_version', 't1.current_version'),
                    $queryBuilder->expr()->eq('t2.object_id', 't1.id'),
                ),
            )->where(
                $queryBuilder->expr()->in(
                    't2.keyword_id',
                    $queryBuilder->createNamedParameter($criterion->value, ArrayParameterType::INTEGER),
                ),
            );

        $fieldDefinitionIds = $this->getSearchableFields($criterion->target);
        if ($fieldDefinitionIds !== null) {
            $subSelect->innerJoin(
                't2',
                'ibexa_content_field',
                't3',
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->eq('t3.id', 't2.objectattribute_id'),
                    $queryBuilder->expr()->eq('t3.version', 't2.objectattribute_version'),
                ),
            )->andWhere(
                $queryBuilder->expr()->in(
                    't3.content_type_field_definition_id',
                    $queryBuilder->createNamedParameter($fieldDefinitionIds, ArrayParameterType::INTEGER),
                ),
            );
        }

        return $queryBuilder->expr()->in('c.id', $subSelect->getSQL());
    }
}
