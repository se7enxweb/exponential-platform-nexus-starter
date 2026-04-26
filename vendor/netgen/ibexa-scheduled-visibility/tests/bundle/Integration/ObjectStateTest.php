<?php

declare(strict_types=1);

namespace Netgen\IbexaScheduledVisibility\Tests\Integration;

use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState as ObjectStateValue;
use Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\VisibilityHandler\ObjectState;
use PHPUnit\Framework\Attributes\DataProvider;

final class ObjectStateTest extends BaseTest
{
    #[DataProvider('provideCases')]
    public function testUpdateVisibility(array $configuration, bool $expectedHidden)
    {
        $scheduledVisibilityService = $this->getScheduledVisibilityService();
        $content = $this->createContent($configuration['publish_from'], $configuration['publish_to']);
        $hiddenObjectState = $this->createObjectState();
        $visibleObjectStateId = 1;
        $objectStateGroupId = 2;
        $handler = $this->getObjectStateHandler($objectStateGroupId, $hiddenObjectState->id, $visibleObjectStateId);
        if ($scheduledVisibilityService->shouldBeHidden($content) && !$handler->isHidden($content)) {
            $handler->hide($content);
        }
        if ($scheduledVisibilityService->shouldBeVisible($content) && $handler->isHidden($content)) {
            $handler->reveal($content);
        }
        $content = $this->getRepository()->getContentService()->loadContent($content->contentInfo->id);

        $objectStateService = $this->getRepository()->getObjectStateService();
        $objectStateGroup = $objectStateService->loadObjectStateGroup(2);
        $objectState = $this->getRepository()->sudo(
            static fn (): ObjectStateValue => $objectStateService->getContentState($content->contentInfo, $objectStateGroup),
        );
        self::assertEquals($objectState->id, $expectedHidden ? $hiddenObjectState->id : $visibleObjectStateId);
    }

    private function getObjectStateHandler(int $objectStateGroupId, int $hiddenObjectStateId, int $visibleObjectStateId): ObjectState
    {
        $repository = $this->getRepository();

        return new ObjectState($repository, $repository->getObjectStateService(), $objectStateGroupId, $hiddenObjectStateId, $visibleObjectStateId);
    }

    private function createObjectState(): ObjectStateValue
    {
        $repository = $this->getRepository();

        $objectStateService = $repository->getObjectStateService();
        $objectStateGroup = $objectStateService->loadObjectStateGroup(2);

        $objectStateCreateStruct = $objectStateService->newObjectStateCreateStruct(
            'scheduled_visibility_object_state',
        );
        $objectStateCreateStruct->priority = 23;
        $objectStateCreateStruct->defaultLanguageCode = 'eng-GB';
        $objectStateCreateStruct->names = [
            'eng-GB' => 'Scheduled visibility object state',
        ];

        return $objectStateService->createObjectState(
            $objectStateGroup,
            $objectStateCreateStruct,
        );
    }
}
