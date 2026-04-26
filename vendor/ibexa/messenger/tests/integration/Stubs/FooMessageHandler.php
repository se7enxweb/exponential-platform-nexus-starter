<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Messenger\Stubs;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class FooMessageHandler
{
    /** @var array<\Ibexa\Tests\Integration\Messenger\Stubs\FooMessage> */
    private array $handledMessages = [];

    public function __invoke(FooMessage $message): void
    {
        $this->handledMessages[] = $message;
    }

    /**
     * @return array<\Ibexa\Tests\Integration\Messenger\Stubs\FooMessage>
     */
    public function getHandledMessages(): array
    {
        return $this->handledMessages;
    }
}
