<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\VisibilityHandler;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content as ContentValue;
use Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\VisibilityHandler;

final class Content extends VisibilityHandler
{
    public function __construct(
        private readonly Repository $repository,
        private readonly ContentService $contentService,
    ) {}

    public function hide(ContentValue $content): void
    {
        $this->repository->sudo(
            fn () => $this->contentService->hideContent($content->contentInfo),
        );
    }

    public function reveal(ContentValue $content): void
    {
        $this->repository->sudo(
            fn () => $this->contentService->revealContent($content->contentInfo),
        );
    }

    public function isHidden(ContentValue $content): bool
    {
        return $content->contentInfo->isHidden;
    }

    public function isVisible(ContentValue $content): bool
    {
        return !$content->contentInfo->isHidden;
    }
}
