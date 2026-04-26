<?php

/*
 * This file is part of the FOSHttpCacheBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\HttpCacheBundle\Security\Http\Logout;

use FOS\HttpCacheBundle\UserContextInvalidator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

final class ContextInvalidationSessionLogoutHandler implements EventSubscriberInterface
{
    public function __construct(
        private readonly UserContextInvalidator $invalidator,
    ) {
    }

    public function onLogout(LogoutEvent $event): void
    {
        if ($event->getRequest()->hasSession()) {
            $this->invalidator->invalidateContext($event->getRequest()->getSession()->getId());
            $event->getRequest()->getSession()->invalidate();
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogout',
        ];
    }
}
