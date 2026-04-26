<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\VisibilityHandler;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\VisibilityHandler;

final class Location extends VisibilityHandler
{
    public function __construct(
        private readonly Repository $repository,
        private readonly LocationService $locationService,
    ) {}

    public function hide(Content $content): void
    {
        $this->repository->sudo(
            function () use ($content): void {
                $locations = $this->locationService->loadLocations($content->contentInfo);

                foreach ($locations as $location) {
                    $this->locationService->hideLocation($location);
                }
            },
        );
    }

    public function reveal(Content $content): void
    {
        $this->repository->sudo(
            function () use ($content): void {
                $locations = $this->locationService->loadLocations($content->contentInfo);

                foreach ($locations as $location) {
                    $this->locationService->unhideLocation($location);
                }
            },
        );
    }

    public function isHidden(Content $content): bool
    {
        return !$this->isVisible($content);
    }

    public function isVisible(Content $content): bool
    {
        $locations = $this->repository->sudo(
            fn () => $this->locationService->loadLocations($content->contentInfo),
        );

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location */
        foreach ($locations as $location) {
            if ($location->hidden) {
                return false;
            }
        }

        return true;
    }
}
