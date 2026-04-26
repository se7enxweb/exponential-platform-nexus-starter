<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\API\Values\Content\Query\SortClause\Target;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\Target;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\SubdocumentQuery;

final class SubdocumentTarget extends Target
{
    /**
     * Identifier of a targeted Content subdocument.
     *
     * @var string
     */
    public $documentTypeIdentifier;

    /**
     * One of the ScoringMode* constants.
     *
     * @see \Netgen\IbexaSearchExtra\API\Values\Content\Query\SortClause\SubdocumentField
     *
     * @var string
     */
    public $scoringMode;

    /**
     * Optional criterion targeting Content subdocument.
     *
     * @var \Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\SubdocumentQuery
     */
    public $subdocumentQuery;

    /**
     * @param string $documentTypeIdentifier
     * @param string $scoringMode
     * @param \Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\SubdocumentQuery|null $subdocumentQuery
     */
    public function __construct(
        string $documentTypeIdentifier,
        string $scoringMode,
        ?SubdocumentQuery $subdocumentQuery = null
    ) {
        $this->documentTypeIdentifier = $documentTypeIdentifier;
        $this->scoringMode = $scoringMode;
        $this->subdocumentQuery = $subdocumentQuery;
    }
}
