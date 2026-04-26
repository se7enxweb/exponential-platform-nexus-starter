<?php

declare(strict_types=1);

namespace Netgen\IbexaScheduledVisibility\Tests\Integration;

use Ibexa\Contracts\Core\Repository\Values\Content\Section as SectionValue;
use Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\VisibilityHandler\Section;
use PHPUnit\Framework\Attributes\DataProvider;

final class SectionTest extends BaseTest
{
    #[DataProvider('provideCases')]
    public function testUpdateVisibility(array $configuration, bool $expectedHidden)
    {
        $scheduledVisibilityService = $this->getScheduledVisibilityService();
        $content = $this->createContent($configuration['publish_from'], $configuration['publish_to']);
        $hiddenSection = $this->createSection();
        $visibleSectionId = 1;
        $handler = $this->getSectionHandler($hiddenSection->id, $visibleSectionId);
        if ($scheduledVisibilityService->shouldBeHidden($content) && !$handler->isHidden($content)) {
            $handler->hide($content);
        }
        if ($scheduledVisibilityService->shouldBeVisible($content) && $handler->isHidden($content)) {
            $handler->reveal($content);
        }
        $content = $this->getRepository()->getContentService()->loadContent($content->contentInfo->id);
        self::assertEquals($content->contentInfo->sectionId, $expectedHidden ? $hiddenSection->id : $visibleSectionId);
    }

    private function getSectionHandler(int $hiddenSectionId, int $visibleSectionId): Section
    {
        $repository = $this->getRepository();

        return new Section($repository, $repository->getSectionService(), $hiddenSectionId, $visibleSectionId);
    }

    private function createSection(): SectionValue
    {
        $repository = $this->getRepository();

        $sectionService = $repository->getSectionService();

        $sectionCreate = $sectionService->newSectionCreateStruct();
        $sectionCreate->name = 'Test Scheduled Visibility section';
        $sectionCreate->identifier = 'scheduled_visibility_section_';

        return $sectionService->createSection($sectionCreate);
    }
}
