<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Solr\Query\Content\SortClauseVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Ibexa\Contracts\Solr\Query\SortClauseVisitor;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\SubdocumentQuery;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\SortClause\SubdocumentField as SubdocumentFieldCriterion;
use RuntimeException;
use function preg_replace;

class SubdocumentField extends SortClauseVisitor
{
    private CriterionVisitor $subdocumentQueryCriterionVisitor;

    public function __construct(CriterionVisitor $subdocumentQueryCriterionVisitor)
    {
        $this->subdocumentQueryCriterionVisitor = $subdocumentQueryCriterionVisitor;
    }

    public function canVisit(SortClause $sortClause): bool
    {
        return $sortClause instanceof SubdocumentFieldCriterion;
    }

    public function visit(SortClause $sortClause): string
    {
        /** @var \Netgen\IbexaSearchExtra\API\Values\Content\Query\SortClause\Target\SubdocumentTarget $target */
        $target = $sortClause->targetData;
        $condition = "document_type_id:{$target->documentTypeIdentifier}";

        if ($target->subdocumentQuery instanceof SubdocumentQuery) {
            /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion $filter */
            $filter = $target->subdocumentQuery->value;
            $queryCondition = $this->subdocumentQueryCriterionVisitor->visit($filter);
            $queryCondition = $this->escapeQuote($queryCondition);

            $condition .= ' AND ' . $queryCondition;
        }

        $condition .= " AND {!func}{$sortClause->target}";
        $scoringMode = $this->resolveScoringMode($target->scoringMode);

        return "{!parent which='document_type_id:content' score='{$scoringMode}' v='{$condition}'}" . $this->getDirection($sortClause);
    }

    private function resolveScoringMode($mode): string
    {
        switch ($mode) {
            case SubdocumentFieldCriterion::ScoringModeNone:
                return 'none';

            case SubdocumentFieldCriterion::ScoringModeAverage:
                return 'avg';

            case SubdocumentFieldCriterion::ScoringModeMaximum:
                return 'max';

            case SubdocumentFieldCriterion::ScoringModeTotal:
                return 'total';

            case SubdocumentFieldCriterion::ScoringModeMinimum:
                return 'min';
        }

        throw new RuntimeException(
            "Scoring mode '{$mode}' is not handled",
        );
    }

    /**
     * Escapes given $string for wrapping inside single or double quotes.
     *
     * Does not include quotes in the returned string, this needs to be done by the consumer code.
     */
    private function escapeQuote(string $string, bool $doubleQuote = false): string
    {
        $pattern = ($doubleQuote ? '/("|\\\)/' : '/(\'|\\\)/');

        return preg_replace($pattern, '\\\$1', $string);
    }
}
