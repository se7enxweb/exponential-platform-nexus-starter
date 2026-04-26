<?php

declare(strict_types=1);

namespace Netgen\TagsBundle\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\Tags;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Netgen\TagsBundle\API\Repository\Values\Content\Query\Criterion\TagKeyword as TagKeywordCriterion;
use Netgen\TagsBundle\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\Tags;

use function count;

final class TagKeyword extends Tags
{
    public function accept(CriterionInterface $criterion): bool
    {
        return $criterion instanceof TagKeywordCriterion;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion $criterion
     */
    public function handle(CriteriaConverter $converter, QueryBuilder $queryBuilder, CriterionInterface $criterion, array $languageSettings): string
    {
        /** @var \Netgen\TagsBundle\API\Repository\Values\Content\Query\Criterion\Value\TagKeywordValue|null $valueData */
        $valueData = $criterion->valueData;

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
            )->innerJoin(
                't2',
                'eztags',
                't3',
                $queryBuilder->expr()->eq('t2.keyword_id', 't3.id'),
            )->leftJoin(
                't3',
                'eztags_keyword',
                't4',
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->eq('t3.id', 't4.keyword_id'),
                    $queryBuilder->expr()->eq('t4.status', 1),
                ),
            );

        if ($valueData !== null && count($valueData->languages ?? []) > 0) {
            if ($valueData->useAlwaysAvailable) {
                $subSelect->where(
                    $queryBuilder->expr()->or(
                        $queryBuilder->expr()->in(
                            't4.locale',
                            $queryBuilder->createNamedParameter($valueData->languages, ArrayParameterType::STRING),
                        ),
                        $queryBuilder->expr()->eq(
                            't3.main_language_id',
                            $this->connection->getDatabasePlatform()->getBitAndComparisonExpression(
                                't4.language_id',
                                -2, // -2 == PHP_INT_MAX << 1
                            ),
                        ),
                    ),
                );
            } else {
                $subSelect->where(
                    $queryBuilder->expr()->in(
                        't4.locale',
                        $queryBuilder->createNamedParameter($valueData->languages, ArrayParameterType::STRING),
                    ),
                );
            }
        }

        /** @var string[] $criterionValue */
        $criterionValue = $criterion->value;

        if ($criterion->operator === Criterion\Operator::LIKE) {
            $subSelect->where(
                $queryBuilder->expr()->like(
                    't4.keyword',
                    $queryBuilder->createNamedParameter($criterionValue[0], Types::STRING),
                ),
            );
        } else {
            $subSelect->where(
                $queryBuilder->expr()->in(
                    't4.keyword',
                    $queryBuilder->createNamedParameter($criterionValue, ArrayParameterType::STRING),
                ),
            );
        }

        $fieldDefinitionIds = $this->getSearchableFields($criterion->target);
        if ($fieldDefinitionIds !== null) {
            $subSelect->innerJoin(
                't2',
                'ibexa_content_field',
                't5',
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->eq('t5.id', 't2.objectattribute_id'),
                    $queryBuilder->expr()->eq('t5.version', 't2.objectattribute_version'),
                ),
            );

            $subSelect->where(
                $queryBuilder->expr()->in(
                    't5.content_type_field_definition_id',
                    $queryBuilder->createNamedParameter($fieldDefinitionIds, ArrayParameterType::INTEGER),
                ),
            );
        }

        return $queryBuilder->expr()->in('c.id', $subSelect->getSQL());
    }
}
