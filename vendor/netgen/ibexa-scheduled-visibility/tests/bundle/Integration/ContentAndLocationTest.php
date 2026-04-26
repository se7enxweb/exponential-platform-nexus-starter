<?php

declare(strict_types=1);

namespace Netgen\IbexaScheduledVisibility\Tests\Integration;

use Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\VisibilityHandler\Content;
use Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\VisibilityHandler\ContentAndLocation;
use Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\VisibilityHandler\Location;
use PHPUnit\Framework\Attributes\DataProvider;

final class ContentAndLocationTest extends BaseTest
{
    #[DataProvider('provideCases')]
    public function testUpdateVisibility(array $configuration, bool $expectedHidden)
    {
        $scheduledVisibilityService = $this->getScheduledVisibilityService();
        $content = $this->createContent($configuration['publish_from'], $configuration['publish_to']);
        $handler = $this->getContentAndLocationHandler();
        if ($scheduledVisibilityService->shouldBeHidden($content) && !$handler->isHidden($content)) {
            $handler->hide($content);
        }
        if ($scheduledVisibilityService->shouldBeVisible($content) && $handler->isHidden($content)) {
            $handler->reveal($content);
        }
        $content = $this->getRepository()->getContentService()->loadContent($content->contentInfo->id);
        self::assertEquals($handler->isHidden($content), $expectedHidden);
    }

    private function getContentAndLocationHandler(): ContentAndLocation
    {
        /** @var \Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\VisibilityHandler\Content $contentHandler */
        $contentHandler = $this->getSetupFactory()->getServiceContainer()->get(Content::class);

        /** @var \Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\VisibilityHandler\Location $locationHandler */
        $locationHandler = $this->getSetupFactory()->getServiceContainer()->get(Location::class);

        return new ContentAndLocation($contentHandler, $locationHandler);
    }
}
