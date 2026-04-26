<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserIbexaBundle\EventListener\Ibexa;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Netgen\ContentBrowser\Event\ConfigLoadEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use function in_array;

final class SetLocationContentTypesListener implements EventSubscriberInterface
{
    public function __construct(
        private ConfigResolverInterface $configResolver,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [ConfigLoadEvent::class => 'onConfigLoad'];
    }

    public function onConfigLoad(ConfigLoadEvent $event): void
    {
        if (!in_array($event->itemType, ['ibexa_content', 'ibexa_location'], true)) {
            return;
        }

        if ($event->config->hasParameter('location_content_types')) {
            return;
        }

        $event->config->setParameter(
            'location_content_types',
            $this->configResolver->getParameter(
                'backend.ibexa.location_content_types',
                'netgen_content_browser',
            ),
        );
    }
}
