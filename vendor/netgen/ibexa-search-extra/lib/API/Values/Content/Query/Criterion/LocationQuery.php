<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator\Specifications;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;

/**
 * LocationQuery criterion is used to query Location subdocuments in Content search.
 */
class LocationQuery extends Criterion
{
    /**
     * @throws \InvalidArgumentException
     */
    public function __construct(CriterionInterface $filter)
    {
        parent::__construct(null, null, $filter);
    }

    public function getSpecifications(): array
    {
        return [
            new Specifications(Operator::EQ, Specifications::FORMAT_SINGLE),
        ];
    }
}
