<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\Query;

use Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\FullText;
use OutOfBoundsException;

final class ConfiguredFulltextCriterionFactory
{
    public function __construct(
        private readonly array $boostConfiguration,
    ) {
    }

    public function create(string $value, string $name = 'default'): FullText
    {
        if (!isset($this->boostConfiguration[$name])) {
            throw new OutOfBoundsException(sprintf('Found no boost configuration named "%s".', $name));
        }

        $configuration = $this->boostConfiguration[$name];

        return new FullText(
            $value,
            [
                'contentTypeBoost' => $configuration['content_types'] ?? [],
                'rawFieldsBoost' => $configuration['raw_fields'] ?? [],
                'metaFieldsBoost' => $configuration['meta_fields'] ?? [],
            ]
        );
    }
}
