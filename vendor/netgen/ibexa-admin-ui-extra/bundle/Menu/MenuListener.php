<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaAdminUIExtraBundle\Menu;

use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\AdminUi\Menu\MainMenuBuilder;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class MenuListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly PermissionResolver $permissionResolver,
        private readonly bool $queuesEnabled,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            ConfigureMenuEvent::MAIN_MENU => ['onMenuConfigure', -1],
        ];
    }

    public function onMenuConfigure(ConfigureMenuEvent $event): void
    {
        if (!$this->queuesEnabled) {
            return;
        }

        if (!$this->permissionResolver->hasAccess('queues', 'read')) {
            return;
        }

        $menu = $event->getMenu();

        if (!isset($menu[MainMenuBuilder::ITEM_ADMIN])) {
            return;
        }

        $menu[MainMenuBuilder::ITEM_ADMIN]
            ->addChild('queues', ['route' => 'netgen.ibexa_admin_ui_extra.queues.list'])
            ->setLabel('queues.title')
            ->setExtra('translation_domain', 'netgen_ibexa_admin_ui_extra');
    }
}
