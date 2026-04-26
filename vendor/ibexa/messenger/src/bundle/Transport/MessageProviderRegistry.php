<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Messenger\Transport;

use Ibexa\Contracts\Messenger\Transport\MessageProviderInterface;

final class MessageProviderRegistry implements MessageProviderInterface
{
    /** @var iterable<\Ibexa\Contracts\Messenger\Transport\MessageProviderInterface> */
    private iterable $providers;

    /**
     * @param iterable<\Ibexa\Contracts\Messenger\Transport\MessageProviderInterface> $providers
     */
    public function __construct(
        iterable $providers
    ) {
        $this->providers = $providers;
    }

    public function getHandledClasses(): iterable
    {
        foreach ($this->providers as $provider) {
            yield from $provider->getHandledClasses();
        }
    }
}
