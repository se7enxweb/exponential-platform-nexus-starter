<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Messenger\Middleware;

use Ibexa\Bundle\Messenger\Stamp\SudoStamp;
use Ibexa\Contracts\Core\Repository\Repository;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

final class SudoMiddleware implements MiddlewareInterface
{
    private Repository $repository;

    public function __construct(
        Repository $repository
    ) {
        $this->repository = $repository;
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $stamp = $envelope->last(SudoStamp::class);
        if ($stamp === null) {
            $envelope = $envelope->with(new SudoStamp());

            $envelope = $this->repository->sudo(
                static fn (): Envelope => $stack->next()->handle($envelope, $stack)
            );

            assert($envelope instanceof Envelope);

            return $envelope->withoutStampsOfType(SudoStamp::class);
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
