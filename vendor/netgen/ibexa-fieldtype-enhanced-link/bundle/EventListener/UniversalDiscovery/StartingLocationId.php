<?php

declare(strict_types=1);

namespace Netgen\IbexaFieldTypeEnhancedLinkBundle\EventListener\UniversalDiscovery;

use Ibexa\AdminUi\UniversalDiscovery\Event\ConfigResolveEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StartingLocationId implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ConfigResolveEvent::NAME => ['onUdwConfigResolve'],
        ];
    }

    public function onUdwConfigResolve(ConfigResolveEvent $event): void
    {
        $configName = $event->getConfigName();
        if ('single' !== $configName && 'multiple' !== $configName) {
            return;
        }

        $context = $event->getContext();
        if (
            !isset($context['type'], $context['starting_location_id'])
            || 'object_relation' !== $context['type']
        ) {
            return;
        }

        $config = $event->getConfig();
        $config['starting_location_id'] = $context['starting_location_id'];

        $event->setConfig($config);
    }
}
