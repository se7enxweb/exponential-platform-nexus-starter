<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\VisibilityHandler;

use Ibexa\Contracts\Core\Repository\ObjectStateService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState as ObjectStateValue;
use Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\VisibilityHandler;

final class ObjectState extends VisibilityHandler
{
    public function __construct(
        private readonly Repository $repository,
        private readonly ObjectStateService $objectStateService,
        private readonly int $objectStateGroupId,
        private readonly int $hiddenObjectStateId,
        private readonly int $visibleObjectStateId,
    ) {}

    public function hide(Content $content): void
    {
        $hiddenObjectStateId = $this->hiddenObjectStateId;

        $this->setObjectState($content, $hiddenObjectStateId);
    }

    public function reveal(Content $content): void
    {
        $visibleObjectStateId = $this->visibleObjectStateId;

        $this->setObjectState($content, $visibleObjectStateId);
    }

    public function isHidden(Content $content): bool
    {
        $objectState = $this->getObjectState($content);

        return $this->hiddenObjectStateId === $objectState->id;
    }

    public function isVisible(Content $content): bool
    {
        $objectState = $this->getObjectState($content);

        return $this->visibleObjectStateId === $objectState->id;
    }

    private function setObjectState(Content $content, int $objectStateId): void
    {
        $this->repository->sudo(
            function () use ($content, $objectStateId): void {
                $objectState = $this->objectStateService->loadObjectState($objectStateId);

                $objectStateGroup = $objectState->getObjectStateGroup();

                $this->objectStateService->setContentState(
                    $content->contentInfo,
                    $objectStateGroup,
                    $objectState,
                );
            },
        );
    }

    private function getObjectState(Content $content): ObjectStateValue
    {
        $objectStateGroupId = $this->objectStateGroupId;

        return $this->repository->sudo(
            function () use ($content, $objectStateGroupId): ObjectStateValue {
                $objectStateGroup = $this->objectStateService->loadObjectStateGroup($objectStateGroupId);

                return $this->objectStateService->getContentState($content->contentInfo, $objectStateGroup);
            },
        );
    }
}
