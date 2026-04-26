<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\SiteApi\Tests\Stubs;

use Pagerfanta\Adapter\AdapterInterface;

/**
 * @implements \Pagerfanta\Adapter\AdapterInterface<\Netgen\IbexaSiteApi\API\Values\Location>
 */
final class LocationAdapter implements AdapterInterface
{
    public function getNbResults(): int
    {
        return 0;
    }

    /**
     * @return iterable<int, \Netgen\IbexaSiteApi\API\Values\Location>
     */
    public function getSlice(int $offset, int $length): iterable
    {
        return [];
    }
}
