<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Messenger\Transport\Sender;

use Ibexa\Bundle\Messenger\Transport\Sender\SendersLocator;
use Ibexa\Contracts\Messenger\Transport\MessageProviderInterface;
use Ibexa\Tests\Bundle\Messenger\Transport\Sender\Stub\SampleMessage;
use Ibexa\Tests\Bundle\Messenger\Transport\Sender\Stub\SampleMessageInterface;
use Ibexa\Tests\Bundle\Messenger\Transport\Sender\Stub\SampleMessageParent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Sender\SenderInterface;
use Symfony\Component\Messenger\Transport\Sender\SendersLocatorInterface;
use Traversable;

final class SendersLocatorTest extends TestCase
{
    /** @var \Symfony\Component\Messenger\Transport\Sender\SenderInterface&\PHPUnit\Framework\MockObject\MockObject */
    private SenderInterface $senderMock;

    /** @var \Ibexa\Contracts\Messenger\Transport\MessageProviderInterface&\PHPUnit\Framework\MockObject\MockObject */
    private MessageProviderInterface $messageProviderMock;

    protected function setUp(): void
    {
        $this->senderMock = $this->createMock(SenderInterface::class);
        $this->messageProviderMock = $this->createMock(MessageProviderInterface::class);
    }

    public function testGetSendersWithInnerLocator(): void
    {
        $envelope = new Envelope(new \stdClass());
        $innerSendersLocatorMock = $this->createMock(SendersLocatorInterface::class);
        $innerSender = $this->createMock(SenderInterface::class);
        $innerSendersLocatorMock
            ->expects(self::once())
            ->method('getSenders')
            ->with(self::identicalTo($envelope))
            ->willReturn(new \ArrayIterator(['inner.sender' => $innerSender]));

        $this->messageProviderMock
            ->expects(self::once())
            ->method('getHandledClasses')
            ->willReturn(['stdClass']);

        $locator = new SendersLocator($this->senderMock, $this->messageProviderMock, $innerSendersLocatorMock);
        $senders = $locator->getSenders($envelope);

        $expectedSenders = [
            'inner.sender' => $innerSender,
            'ibexa.messenger.transport' => $this->senderMock,
        ];
        self::assertInstanceOf(Traversable::class, $senders);
        self::assertSame($expectedSenders, iterator_to_array($senders));
    }

    public function testGetSendersWithoutInnerLocator(): void
    {
        $envelope = new Envelope(new \stdClass());
        $this->messageProviderMock
            ->expects(self::once())
            ->method('getHandledClasses')
            ->willReturn(['stdClass']);

        $locator = new SendersLocator($this->senderMock, $this->messageProviderMock, null);
        $senders = $locator->getSenders($envelope);

        $expectedSenders = [
            'ibexa.messenger.transport' => $this->senderMock,
        ];
        self::assertInstanceOf(Traversable::class, $senders);
        self::assertSame($expectedSenders, iterator_to_array($senders));
    }

    public function testGetSendersWithUnhandledClass(): void
    {
        $envelope = new Envelope(new \stdClass());
        $this->messageProviderMock
            ->expects(self::once())
            ->method('getHandledClasses')
            ->willReturn(['AnotherClass']);

        $locator = new SendersLocator($this->senderMock, $this->messageProviderMock, null);
        $senders = $locator->getSenders($envelope);

        $expectedSenders = [];
        self::assertInstanceOf(Traversable::class, $senders);
        self::assertSame($expectedSenders, iterator_to_array($senders));
    }

    public function testListTypesIncludesInterfaces(): void
    {
        $envelope = new Envelope($this->createMock(SampleMessageInterface::class));

        $this->assertMessageIsHandled($envelope);
    }

    public function testListTypesIncludesParents(): void
    {
        $envelope = new Envelope($this->createMock(SampleMessageParent::class));

        $this->assertMessageIsHandled($envelope);
    }

    public function testGetSendersWithMultipleGenerators(): void
    {
        $envelope = new Envelope(new \stdClass());
        $generator = static function (): \Generator {
            yield from ['stdClass'];
            yield from ['AnotherClass'];
        };

        $this->messageProviderMock
            ->expects(self::once())
            ->method('getHandledClasses')
            ->willReturn($generator());

        $locator = new SendersLocator($this->senderMock, $this->messageProviderMock, null);
        $senders = $locator->getSenders($envelope);

        $expectedSenders = [
            'ibexa.messenger.transport' => $this->senderMock,
        ];
        self::assertInstanceOf(Traversable::class, $senders);
        self::assertSame($expectedSenders, iterator_to_array($senders));
    }

    private function assertMessageIsHandled(Envelope $envelope): void
    {
        $this->messageProviderMock
            ->expects(self::once())
            ->method('getHandledClasses')
            ->willReturn([
                SampleMessage::class,
                SampleMessageParent::class,
                SampleMessageInterface::class,
            ]);

        $locator = new SendersLocator($this->senderMock, $this->messageProviderMock, null);
        $senders = $locator->getSenders($envelope);

        $expectedSenders = [
            'ibexa.messenger.transport' => $this->senderMock,
        ];
        self::assertInstanceOf(Traversable::class, $senders);
        self::assertSame($expectedSenders, iterator_to_array($senders));
    }
}
