<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Messenger\EventSubscriber;

use Ibexa\Bundle\Messenger\Stamp\SudoStamp;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\SendMessageToTransportsEvent;

final class RemoveSudoStampOnSendMessageSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            SendMessageToTransportsEvent::class => 'removeSudoStamp',
        ];
    }

    public function removeSudoStamp(SendMessageToTransportsEvent $event): void
    {
        $envelope = $event->getEnvelope();
        $envelope = $envelope->withoutStampsOfType(SudoStamp::class);
        $event->setEnvelope($envelope);
    }
}
