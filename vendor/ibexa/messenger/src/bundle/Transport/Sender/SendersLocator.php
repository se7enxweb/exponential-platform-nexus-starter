<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Messenger\Transport\Sender;

use Ibexa\Contracts\Messenger\Transport\MessageProviderInterface;
use function Ibexa\PolyfillPhp82\iterator_to_array;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Sender\SenderInterface;
use Symfony\Component\Messenger\Transport\Sender\SendersLocatorInterface;

final class SendersLocator implements SendersLocatorInterface
{
    private SenderInterface $sender;

    private MessageProviderInterface $messageProvider;

    private ?SendersLocatorInterface $inner;

    public function __construct(
        SenderInterface $sender,
        MessageProviderInterface $messageProvider,
        ?SendersLocatorInterface $inner
    ) {
        $this->sender = $sender;
        $this->messageProvider = $messageProvider;
        $this->inner = $inner;
    }

    public function getSenders(Envelope $envelope): iterable
    {
        if ($this->inner !== null) {
            yield from $this->inner->getSenders($envelope);
        }

        $classes = iterator_to_array($this->messageProvider->getHandledClasses(), false);

        foreach ($this->listTypes($envelope) as $type) {
            if (in_array($type, $classes, true)) {
                yield 'ibexa.messenger.transport' => $this->sender;

                break;
            }
        }
    }

    /**
     * @return array<class-string>
     */
    private function listTypes(Envelope $envelope): array
    {
        $class = get_class($envelope->getMessage());

        return [$class => $class]
            + class_parents($class)
            + class_implements($class);
    }
}
