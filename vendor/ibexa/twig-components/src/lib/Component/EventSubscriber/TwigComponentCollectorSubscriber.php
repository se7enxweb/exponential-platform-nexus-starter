<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\TwigComponents\Component\EventSubscriber;

use Ibexa\Contracts\TwigComponents\Event\RenderGroupEvent;
use Ibexa\Contracts\TwigComponents\Event\RenderSingleEvent;
use Ibexa\TwigComponents\DataCollector\TwigComponentCollector;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class TwigComponentCollectorSubscriber implements EventSubscriberInterface
{
    private TwigComponentCollector $collector;

    public function __construct(TwigComponentCollector $collector)
    {
        $this->collector = $collector;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RenderGroupEvent::class => ['onRenderGroup', 50],
            RenderSingleEvent::class => ['onRenderSingle', 50],
        ];
    }

    public function onRenderGroup(RenderGroupEvent $event): void
    {
        $this->collector->addAvailableGroups($event->getGroupName());
    }

    public function onRenderSingle(RenderSingleEvent $event): void
    {
        $this->collector->addRenderedComponent($event->getGroupName(), $event->getName(), $event->getComponent());
    }
}
