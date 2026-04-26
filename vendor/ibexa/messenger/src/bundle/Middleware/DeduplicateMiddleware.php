<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Messenger\Middleware;

use Ibexa\Bundle\Messenger\Stamp\DeduplicateStamp;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;

/**
 * (c) Fabien Potencier <fabien@symfony.com>.
 *
 * Original code: https://github.com/symfony/symfony/blob/7.3/src/Symfony/Component/Messenger/Middleware/DeduplicateMiddleware.php
 */
final class DeduplicateMiddleware implements MiddlewareInterface
{
    private LockFactory $lockFactory;

    public function __construct(LockFactory $lockFactory)
    {
        $this->lockFactory = $lockFactory;
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $deduplicateStamp = $envelope->last(DeduplicateStamp::class);
        if ($deduplicateStamp === null) {
            return $stack->next()->handle($envelope, $stack);
        }

        $receivedStamp = $envelope->last(ReceivedStamp::class);
        if ($receivedStamp === null) {
            $lock = $this->lockFactory->createLockFromKey(
                $deduplicateStamp->getKey(),
                $deduplicateStamp->getTtl(),
                false,
            );

            if (!$lock->acquire()) {
                return $envelope;
            }
        } elseif ($deduplicateStamp->onlyDeduplicateInQueue()) {
            $this->lockFactory->createLockFromKey($deduplicateStamp->getKey())->release();
        }

        try {
            $envelope = $stack->next()->handle($envelope, $stack);
        } finally {
            $receivedStamp = $envelope->last(ReceivedStamp::class);
            if ($receivedStamp !== null && !$deduplicateStamp->onlyDeduplicateInQueue()) {
                $this->lockFactory->createLockFromKey($deduplicateStamp->getKey())->release();
            }
        }

        return $envelope;
    }
}
