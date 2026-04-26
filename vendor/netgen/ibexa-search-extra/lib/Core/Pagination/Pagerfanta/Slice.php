<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Pagination\Pagerfanta;

use ArrayAccess;
use ArrayIterator;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use IteratorAggregate;
use RuntimeException;
use function array_key_exists;
use function array_map;

/**
 * Implements IteratorAggregate with access to the array of the SearchHit instances
 * and aggregated ArrayIterator over values contained in them.
 */
final class Slice implements IteratorAggregate, ArrayAccess
{
    /**
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit[]
     */
    private array $searchHits;

    public function __construct(array $searchHits)
    {
        $this->searchHits = $searchHits;
    }

    public function getSearchHits(): array
    {
        return $this->searchHits;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator(
            array_map(
                static fn (SearchHit $searchHit) => $searchHit->valueObject,
                $this->searchHits,
            ),
        );
    }

    #[\ReturnTypeWillChange]
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->searchHits);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->searchHits[$offset]->valueObject;
    }

    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value): void
    {
        throw new RuntimeException('Method ' . __METHOD__ . ' is not supported');
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($offset): void
    {
        throw new RuntimeException('Method ' . __METHOD__ . ' is not supported');
    }
}
