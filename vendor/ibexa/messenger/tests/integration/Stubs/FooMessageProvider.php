<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Messenger\Stubs;

use Ibexa\Contracts\Messenger\Transport\MessageProviderInterface;

final class FooMessageProvider implements MessageProviderInterface
{
    public function getHandledClasses(): iterable
    {
        return [FooMessage::class];
    }
}
