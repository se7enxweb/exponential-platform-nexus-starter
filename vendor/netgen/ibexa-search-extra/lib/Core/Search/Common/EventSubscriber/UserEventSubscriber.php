<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\EventSubscriber;

use Ibexa\Contracts\Core\Repository\Events\User\AssignUserToUserGroupEvent;
use Ibexa\Contracts\Core\Repository\Events\User\BeforeUnAssignUserFromUserGroupEvent;
use Ibexa\Contracts\Core\Repository\Events\User\CreateUserEvent;
use Ibexa\Contracts\Core\Repository\Events\User\CreateUserGroupEvent;
use Ibexa\Contracts\Core\Repository\Events\User\DeleteUserEvent;
use Ibexa\Contracts\Core\Repository\Events\User\DeleteUserGroupEvent;
use Ibexa\Contracts\Core\Repository\Events\User\MoveUserGroupEvent;
use Ibexa\Contracts\Core\Repository\Events\User\UnAssignUserFromUserGroupEvent;
use Ibexa\Contracts\Core\Repository\Events\User\UpdateUserEvent;
use Ibexa\Contracts\Core\Repository\Events\User\UpdateUserGroupEvent;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\User\AssignUserToUserGroup;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\User\BeforeUnAssignUserFromUserGroup;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\User\CreateUser;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\User\CreateUserGroup;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\User\DeleteUser;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\User\DeleteUserGroup;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\User\MoveUserGroup;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\User\UnAssignUserFromUserGroup;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\User\UpdateUser;
use Netgen\IbexaSearchExtra\Core\Search\Common\Messenger\Message\Search\User\UpdateUserGroup;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

class UserEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            CreateUserEvent::class => 'onCreateUser',
            CreateUserGroupEvent::class => 'onCreateUserGroup',
            DeleteUserEvent::class => 'onDeleteUser',
            DeleteUserGroupEvent::class => 'onDeleteUserGroup',
            MoveUserGroupEvent::class => 'onMoveUserGroup',
            UpdateUserEvent::class => 'onUpdateUser',
            UpdateUserGroupEvent::class => 'onUpdateUserGroup',
            AssignUserToUserGroupEvent::class => 'onAssignUserToUserGroup',
            UnAssignUserFromUserGroupEvent::class => 'onUnAssignUserFromUserGroup',
            BeforeUnAssignUserFromUserGroupEvent::class => 'onBeforeUnAssignUserFromUserGroup',
        ];
    }

    public function onCreateUser(CreateUserEvent $event): void
    {
        $this->messageBus->dispatch(
            new CreateUser(
                $event->getUser()->id,
            ),
        );
    }

    public function onCreateUserGroup(CreateUserGroupEvent $event): void
    {
        $this->messageBus->dispatch(
            new CreateUserGroup(
                $event->getUserGroup()->id,
            ),
        );
    }

    public function onDeleteUser(DeleteUserEvent $event): void
    {
        try {
            $mainLocationParentLocationId = $event->getUser()->contentInfo->getMainLocation()?->parentLocationId;
        } catch (Throwable) {
            $mainLocationParentLocationId = null;
        }

        $this->messageBus->dispatch(
            new DeleteUser(
                $event->getUser()->id,
                $event->getLocations(),
                $mainLocationParentLocationId,
            ),
        );
    }

    public function onDeleteUserGroup(DeleteUserGroupEvent $event): void
    {
        try {
            $mainLocationParentLocationId = $event->getUserGroup()->contentInfo->getMainLocation()?->parentLocationId;
        } catch (Throwable) {
            $mainLocationParentLocationId = null;
        }

        $this->messageBus->dispatch(
            new DeleteUserGroup(
                $event->getUserGroup()->id,
                $event->getLocations(),
                $mainLocationParentLocationId,
            ),
        );
    }

    public function onMoveUserGroup(MoveUserGroupEvent $event): void
    {
        $this->messageBus->dispatch(
            new MoveUserGroup(
                $event->getUserGroup()->id,
            ),
        );
    }

    public function onUpdateUser(UpdateUserEvent $event): void
    {
        $this->messageBus->dispatch(
            new UpdateUser(
                $event->getUser()->id,
            ),
        );
    }

    public function onUpdateUserGroup(UpdateUserGroupEvent $event): void
    {
        $this->messageBus->dispatch(
            new UpdateUserGroup(
                $event->getUserGroup()->id,
            ),
        );
    }

    public function onAssignUserToUserGroup(AssignUserToUserGroupEvent $event): void
    {
        $this->messageBus->dispatch(
            new AssignUserToUserGroup(
                $event->getUser()->id,
            ),
        );
    }

    public function onUnAssignUserFromUserGroup(UnAssignUserFromUserGroupEvent $event): void
    {
        $this->messageBus->dispatch(
            new UnAssignUserFromUserGroup(
                $event->getUser()->id,
            ),
        );
    }

    public function onBeforeUnAssignUserFromUserGroup(BeforeUnAssignUserFromUserGroupEvent $event): void
    {
        $this->messageBus->dispatch(
            new BeforeUnAssignUserFromUserGroup(
                $event->getUser()->id,
            ),
        );
    }
}
