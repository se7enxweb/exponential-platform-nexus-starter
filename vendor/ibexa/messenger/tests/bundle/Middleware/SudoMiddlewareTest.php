<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Messenger\Middleware;

use Ibexa\Bundle\Messenger\Middleware\SudoMiddleware;
use Ibexa\Bundle\Messenger\Stamp\SudoStamp;
use Ibexa\Contracts\Core\Repository\Repository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

final class SudoMiddlewareTest extends TestCase
{
    /** @var \Ibexa\Contracts\Core\Repository\Repository&\PHPUnit\Framework\MockObject\MockObject */
    private Repository $repository;

    /** @var \Symfony\Component\Messenger\Middleware\StackInterface&\PHPUnit\Framework\MockObject\MockObject */
    private StackInterface $stack;

    private SudoMiddleware $middleware;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(Repository::class);
        $this->stack = $this->createMock(StackInterface::class);
        $this->middleware = new SudoMiddleware($this->repository);
    }

    public function testHandleAddsSudoStampWhenNoneExists(): void
    {
        $envelope = new Envelope(new \stdClass());

        $nextMiddleware = $this->createMock(MiddlewareInterface::class);
        $nextMiddleware
            ->expects(self::once())
            ->method('handle')
            ->with(self::callback(static function (Envelope $envelope): bool {
                self::assertNotNull($envelope->last(SudoStamp::class));

                return true;
            }))
            ->willReturnArgument(0);

        $this->stack
            ->expects(self::once())
            ->method('next')
            ->willReturn($nextMiddleware);

        $this->repository
            ->expects(self::once())
            ->method('sudo')
            ->willReturnCallback(static function (callable $callback): Envelope {
                $envelope = $callback();
                self::assertInstanceOf(Envelope::class, $envelope);

                return $envelope;
            });

        $processedEnvelope = $this->middleware->handle($envelope, $this->stack);

        self::assertNotSame($envelope, $processedEnvelope);
        self::assertSame($envelope->getMessage(), $processedEnvelope->getMessage());
        self::assertNull($processedEnvelope->last(SudoStamp::class));
    }

    public function testHandleDoesNotAddSudoStampWhenOneExists(): void
    {
        $stamp = new SudoStamp();
        $envelope = new Envelope(new \stdClass(), [$stamp]);
        $nextMiddleware = $this->createMock(MiddlewareInterface::class);

        $this->stack
            ->expects(self::once())
            ->method('next')
            ->willReturn($nextMiddleware);

        $nextMiddleware
            ->expects(self::once())
            ->method('handle')
            ->with(self::callback(static function (Envelope $envelope): bool {
                self::assertNotNull($envelope->last(SudoStamp::class));

                return true;
            }))
            ->willReturnArgument(0);

        $this->repository
            ->expects(self::never())
            ->method('sudo');

        $processedEnvelope = $this->middleware->handle($envelope, $this->stack);

        self::assertSame($envelope->getMessage(), $processedEnvelope->getMessage());
        self::assertSame($stamp, $processedEnvelope->last(SudoStamp::class));
    }
}
