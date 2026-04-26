<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Tests\API;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\FullText as BaseFullTextCriterion;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\FulltextSpellcheck;
use Netgen\IbexaSearchExtra\API\Values\Content\SpellcheckQuery;

class FullTextCriterion extends BaseFullTextCriterion implements FulltextSpellcheck
{
    public function getSpellcheckQuery(): SpellcheckQuery
    {
        $spellcheckQuery = new SpellcheckQuery();
        $spellcheckQuery->query = $this->value;
        $spellcheckQuery->count = 10;

        return $spellcheckQuery;
    }
}
