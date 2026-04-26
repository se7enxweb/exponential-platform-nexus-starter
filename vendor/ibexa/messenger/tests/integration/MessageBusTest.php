<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Messenger;

use Ibexa\Bundle\Messenger\Stamp\DeduplicateStamp;
use Ibexa\Contracts\Test\Core\IbexaKernelTestCase;
use Ibexa\Tests\Integration\Messenger\Stubs\FooMessage;
use Ibexa\Tests\Integration\Messenger\Stubs\FooMessageHandler;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;

final class MessageBusTest extends IbexaKernelTestCase
{
    private MessageBusInterface $bus;

    private ReceiverInterface $receiver;

    private FooMessageHandler $fooHandler;

    protected function setUp(): void
    {
        $core = self::getIbexaTestCore();
        $this->bus = $core->getServiceByClassName(MessageBusInterface::class, 'ibexa.messenger.bus');
        $this->receiver = $core->getServiceByClassName(ReceiverInterface::class, 'ibexa.messenger.transport');
        $this->fooHandler = $core->getServiceByClassName(FooMessageHandler::class);
    }

    public function testBus(): void
    {
        for ($i = 0; $i < 5; ++$i) {
            $this->bus->dispatch(new FooMessage());
        }

        $messages = $this->getMessagesFromReceiver();

        self::assertCount(5, $messages);
        self::assertCount(0, $this->fooHandler->getHandledMessages());
    }

    public function testDeduplication(): void
    {
        for ($i = 0; $i < 5; ++$i) {
            $this->bus->dispatch(new Envelope(new FooMessage(), [
                new DeduplicateStamp('foo-message'),
            ]));
        }

        $receivedMessages = $this->getMessagesFromReceiver();

        self::assertCount(1, $receivedMessages);
        self::assertCount(0, $this->fooHandler->getHandledMessages());

        $message = $receivedMessages[0];
        $message = Envelope::wrap($message, [new ReceivedStamp('test')]);

        // Removes deduplication lock from non-transactional stores
        $this->bus->dispatch($message);
        self::assertCount(1, $this->fooHandler->getHandledMessages());
    }

    public function testHandlingReceivedMessages(): void
    {
        // Symfony\Component\Messenger\Worker adds ReceivedStamp to prevent re-sending messages back to the queue.
        $message = Envelope::wrap(new FooMessage(), [
            new ReceivedStamp('test'),
        ]);

        $this->bus->dispatch($message);
        self::assertCount(1, $this->fooHandler->getHandledMessages());
    }

    public function testHandlingMessagesUsingCommand(): void
    {
        $this->bus->dispatch(new Envelope(new FooMessage()));

        $kernel = self::getContainer()->get('kernel');
        self::assertInstanceOf(AbstractTestKernel::class, $kernel);
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            '--bus' => 'ibexa.messenger.bus',
            // --limit is another option, but in cases where messages remain in queue between tests this may cause
            // confusion, and it is actually a failure state.
            '--time-limit' => 3,
            'command' => 'messenger:consume',
            'receivers' => ['ibexa.messenger.transport'],
        ]);

        $application->run($input, new NullOutput());
        self::assertCount(1, $this->fooHandler->getHandledMessages());
    }

    /**
     * @return array<\Symfony\Component\Messenger\Envelope>
     */
    private function getMessagesFromReceiver(): array
    {
        $receivedMessages = [];
        do {
            $messages = $this->receiver->get();
            $messageFound = false;

            foreach ($messages as $message) {
                $receivedMessages[] = $message;

                // Acknowledging the message early to remove it from Redis queue
                $this->receiver->ack($message);
                $messageFound = true;
            }
        } while ($messageFound);

        return $receivedMessages;
    }
}
