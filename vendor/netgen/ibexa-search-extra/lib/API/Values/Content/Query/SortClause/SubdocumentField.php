<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\API\Values\Content\Query\SortClause;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\SubdocumentQuery;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\SortClause\Target\SubdocumentTarget;

/**
 * SubdocumentField sort clause is used to sort Content by field in matched subdocument.
 */
final class SubdocumentField extends SortClause
{
    public const ScoringModeNone = 'ScoringModeNone';
    public const ScoringModeAverage = 'ScoringModeAvg';
    public const ScoringModeMaximum = 'ScoringModeMax';
    public const ScoringModeTotal = 'ScoringModeTotal';
    public const ScoringModeMinimum = 'ScoringModeMin';

    /**
     * @param string $fieldName
     * @param string $documentTypeIdentifier
     * @param string $scoringMode
     * @param string $sortDirection
     * @param \Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\SubdocumentQuery|null $subdocumentQuery
     */
    public function __construct(
        string $fieldName,
        string $documentTypeIdentifier,
        string $scoringMode = self::ScoringModeNone,
        string $sortDirection = Query::SORT_ASC,
        ?SubdocumentQuery $subdocumentQuery = null
    ) {
        parent::__construct(
            $fieldName,
            $sortDirection,
            new SubdocumentTarget(
                $documentTypeIdentifier,
                $scoringMode,
                $subdocumentQuery,
            ),
        );
    }
}
