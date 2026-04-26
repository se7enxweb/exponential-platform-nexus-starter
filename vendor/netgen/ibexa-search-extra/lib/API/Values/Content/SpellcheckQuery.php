<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\API\Values\Content;

class SpellcheckQuery
{
    /**
     * This parameter specifies the query to spellcheck.
     */
    public string $query;

    /**
     * This parameter specifies the maximum number of suggestions
     * that the spellchecker should return for a term.
     */
    public int $count;

    /**
     * Additional Solr spellcheck params as an array to be encoded with \json_encode().
     *
     * Example:
     *
     * ```php
     *  $query->parameters = [
     *      'build': true,
     *      'reload' => true,
     *      'onlyMorePopular' => false,
     *      'maxResultsForSuggest' => 5,
     *  ];
     * ```
     */
    public array $parameters = [];
}
