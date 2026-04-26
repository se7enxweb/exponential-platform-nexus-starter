<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Legacy\Query\Common\CriterionHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Persistence\Content\Language\Handler as LanguageHandler;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Core\Persistence\Legacy\Content\Gateway;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\ContentName as ContentNameCriterion;
use RuntimeException;

use function addcslashes;
use function count;
use function reset;
use function sprintf;
use function str_replace;

/**
 * @see \Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\ContentName
 */
final class ContentName extends CriterionHandler
{
    protected LanguageHandler $languageHandler;

    public function __construct(Connection $connection, LanguageHandler $languageHandler)
    {
        parent::__construct($connection);

        $this->languageHandler = $languageHandler;
    }

    public function accept(CriterionInterface $criterion): bool
    {
        return $criterion instanceof ContentNameCriterion;
    }

    /**
     * @param \Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter $converter
     * @param \Doctrine\DBAL\Query\QueryBuilder $queryBuilder
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface $criterion
     * @param array $languageSettings
     *
     * @return string
     */
    public function handle(
        CriteriaConverter $converter,
        QueryBuilder $queryBuilder,
        CriterionInterface $criterion,
        array $languageSettings
    ): string {
        $subQueryBuilder = $this->connection->createQueryBuilder();

        $subQueryBuilder
            ->select('contentobject_id')
            ->from(Gateway::CONTENT_NAME_TABLE)
            ->where(
                $subQueryBuilder->expr()->and(
                    $this->getCriterionCondition($queryBuilder, $subQueryBuilder, $criterion),
                    $this->getLanguageCondition($queryBuilder, $subQueryBuilder, $languageSettings),
                ),
            );

        return $queryBuilder->expr()->in(
            'c.id',
            $subQueryBuilder->getSQL(),
        );
    }

    /**
     * @param \Doctrine\DBAL\Query\QueryBuilder $queryBuilder
     * @param \Doctrine\DBAL\Query\QueryBuilder $subQueryBuilder
     * @param array $languageSettings
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     *
     * @return \Doctrine\DBAL\Query\Expression\CompositeExpression|string
     */
    protected function getLanguageCondition(
        QueryBuilder $queryBuilder,
        QueryBuilder $subQueryBuilder,
        array $languageSettings
    ) {
        // 1. Use main language(s) by default
        if (empty($languageSettings['languages'])) {
            return $subQueryBuilder->expr()->gt(
                $this->dbPlatform->getBitAndComparisonExpression(
                    'c.initial_language_id',
                    Gateway::CONTENT_NAME_TABLE . '.language_id',
                ),
                $queryBuilder->createNamedParameter(0, ParameterType::INTEGER),
            );
        }

        // 2. Otherwise, use prioritized languages
        $leftSide = $this->dbPlatform->getBitAndComparisonExpression(
            sprintf(
                'c.language_mask - %s',
                $this->dbPlatform->getBitAndComparisonExpression(
                    'c.language_mask',
                    Gateway::CONTENT_NAME_TABLE . '.language_id',
                ),
            ),
            $queryBuilder->createNamedParameter(1, ParameterType::INTEGER),
        );
        $rightSide = $this->dbPlatform->getBitAndComparisonExpression(
            Gateway::CONTENT_NAME_TABLE . '.language_id',
            $queryBuilder->createNamedParameter(1, ParameterType::INTEGER),
        );

        for (
            $index = count($languageSettings['languages']) - 1, $multiplier = 2;
            $index >= 0;
            $index--, $multiplier *= 2
        ) {
            $languageCode = $languageSettings['languages'][$index];
            $languageId = $this->languageHandler->loadByLanguageCode($languageCode)->id;

            $addToLeftSide = $this->dbPlatform->getBitAndComparisonExpression(
                sprintf(
                    'c.language_mask - %s',
                    $this->dbPlatform->getBitAndComparisonExpression(
                        'c.language_mask',
                        Gateway::CONTENT_NAME_TABLE . '.language_id',
                    ),
                ),
                $queryBuilder->createNamedParameter($languageId, ParameterType::INTEGER),
            );
            $addToRightSide = $this->dbPlatform->getBitAndComparisonExpression(
                Gateway::CONTENT_NAME_TABLE . '.language_id',
                $queryBuilder->createNamedParameter($languageId, ParameterType::INTEGER),
            );

            if ($multiplier > $languageId) {
                $factor = $multiplier / $languageId;
                /* @noinspection PhpStatementHasEmptyBodyInspection */
                /* @noinspection MissingOrEmptyGroupStatementInspection */
                for ($shift = 0; $factor > 1; $factor /= 2, $shift++) /** @noinspection SuspiciousSemicolonInspection */ ;
                $factorTerm = ' << ' . $shift;
                $addToLeftSide .= $factorTerm;
                $addToRightSide .= $factorTerm;
            } elseif ($multiplier < $languageId) {
                $factor = $languageId / $multiplier;
                /* @noinspection PhpStatementHasEmptyBodyInspection */
                /* @noinspection MissingOrEmptyGroupStatementInspection */
                for ($shift = 0; $factor > 1; $factor /= 2, $shift++) /** @noinspection SuspiciousSemicolonInspection */ ;
                $factorTerm = ' >> ' . $shift;
                $addToLeftSide .= $factorTerm;
                $addToRightSide .= $factorTerm;
            }

            $leftSide = "{$leftSide} + ({$addToLeftSide})";
            $rightSide = "{$rightSide} + ({$addToRightSide})";
        }

        return $subQueryBuilder->expr()->and(
            $subQueryBuilder->expr()->gt(
                $this->dbPlatform->getBitAndComparisonExpression(
                    'c.language_mask',
                    Gateway::CONTENT_NAME_TABLE . '.language_id',
                ),
                $queryBuilder->createNamedParameter(0, ParameterType::INTEGER),
            ),
            $subQueryBuilder->expr()->lt($leftSide, $rightSide),
        );
    }

    /**
     * Returns the given $string prepared for use in SQL LIKE clause.
     *
     * LIKE clause wildcards '%' and '_' contained in the given $string will be escaped.
     *
     * @param $string
     *
     * @return string
     */
    protected function prepareLikeString($string): string
    {
        $string = addcslashes($string, '%_');

        return str_replace('*', '%', $string);
    }

    private function getCriterionCondition(
        QueryBuilder $queryBuilder,
        QueryBuilder $subQueryBuilder,
        CriterionInterface $criterion
    ): string {
        $column = Gateway::CONTENT_NAME_TABLE . '.name';

        switch ($criterion->operator) {
            case Operator::EQ:
            case Operator::IN:
                return $subQueryBuilder->expr()->in(
                    $column,
                    $queryBuilder->createNamedParameter($criterion->value, Connection::PARAM_STR_ARRAY),
                );

            case Operator::GT:
            case Operator::GTE:
            case Operator::LT:
            case Operator::LTE:
                $operatorFunction = $this->comparatorMap[$criterion->operator];

                return $subQueryBuilder->expr()->{$operatorFunction}(
                    $column,
                    $queryBuilder->createNamedParameter(reset($criterion->value), ParameterType::STRING)
                );

            case Operator::BETWEEN:
                return $this->dbPlatform->getBetweenExpression(
                    $column,
                    $queryBuilder->createNamedParameter($criterion->value[0], ParameterType::STRING),
                    $queryBuilder->createNamedParameter($criterion->value[1], ParameterType::STRING),
                );

            case Operator::LIKE:
                $string = $this->prepareLikeString(reset($criterion->value));

                return $subQueryBuilder->expr()->like(
                    $column,
                    $queryBuilder->createNamedParameter($string, ParameterType::STRING),
                );

            default:
                throw new RuntimeException(
                    "Unknown operator '{$criterion->operator}' for ContentId criterion handler.",
                );
        }
    }
}
