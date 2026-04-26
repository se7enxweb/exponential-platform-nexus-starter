<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Solr\Query\Common\SortClauseVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Contracts\Solr\Query\SortClauseVisitor;
use Netgen\IbexaSearchExtra\API\Values\Content\Query\SortClause\ContentName as ContentNameClause;

class ContentName extends SortClauseVisitor
{
    public function canVisit(SortClause $sortClause): bool
    {
        return $sortClause instanceof ContentNameClause;
    }

    public function visit(SortClause $sortClause): string
    {
        return 'ng_content_name_s' . $this->getDirection($sortClause);
    }
}
