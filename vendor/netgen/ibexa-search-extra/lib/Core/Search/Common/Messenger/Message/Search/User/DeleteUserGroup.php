<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\User;

final class DeleteUserGroup
{
    /**
     * @param int[] $locationIds
     */
    public function __construct(
        public readonly int $contentId,
        public readonly array $locationIds,
        public readonly ?int $mainLocationParentLocationId,
    ) {}
}
