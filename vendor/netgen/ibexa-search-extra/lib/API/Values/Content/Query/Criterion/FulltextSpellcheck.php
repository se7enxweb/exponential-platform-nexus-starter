<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion;

use Netgen\IbexaSearchExtra\API\Values\Content\SpellcheckQuery;

interface FulltextSpellcheck
{
    /**
     * Gets query to be used for spell check.
     */
    public function getSpellcheckQuery(): SpellcheckQuery;
}
