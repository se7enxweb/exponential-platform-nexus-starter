<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Elasticsearch\Query\CriterionVisitor\Content;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\Query\CriterionVisitor\AbstractTermVisitor;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\Visible;

class VisibilityVisitor extends AbstractTermVisitor
{
    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof Visible;
    }

    protected function getTargetField(Criterion $criterion): string
    {
        return 'ng_content_visible_b';
    }

    protected function getTargetValue(Criterion $criterion): bool
    {
        /** @var array<bool> $value */
        $value = $criterion->value;

        return $value[0] === true;
    }
}
