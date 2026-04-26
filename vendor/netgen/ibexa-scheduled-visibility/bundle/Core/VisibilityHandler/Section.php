<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\VisibilityHandler;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\SectionService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\VisibilityHandler;

final class Section extends VisibilityHandler
{
    public function __construct(
        private readonly Repository $repository,
        private readonly SectionService $sectionService,
        private readonly int $hiddenSectionId,
        private readonly int $visibleSectionId,
    ) {}

    public function hide(Content $content): void
    {
        $hiddenSectionId = $this->hiddenSectionId;

        $this->assignSection($content, $hiddenSectionId);
    }

    public function reveal(Content $content): void
    {
        $visibleSectionId = $this->visibleSectionId;

        $this->assignSection($content, $visibleSectionId);
    }

    public function isHidden(Content $content): bool
    {
        return $content->contentInfo->sectionId === $this->hiddenSectionId;
    }

    public function isVisible(Content $content): bool
    {
        return $content->contentInfo->sectionId === $this->visibleSectionId;
    }

    private function assignSection(Content $content, int $sectionId): void
    {
        $this->repository->sudo(
            function () use ($content, $sectionId): void {
                $section = $this->sectionService->loadSection($sectionId);

                $this->sectionService->assignSection($content->contentInfo, $section);
            },
        );
    }
}
