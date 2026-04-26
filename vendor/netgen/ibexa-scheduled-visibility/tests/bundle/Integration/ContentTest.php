<?php

declare(strict_types=1);

namespace Netgen\IbexaScheduledVisibility\Tests\Integration;

use Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\VisibilityHandler\Content;
use PHPUnit\Framework\Attributes\DataProvider;

final class ContentTest extends BaseTest
{
    #[DataProvider('provideCases')]
    public function testUpdateVisibility(array $configuration, bool $expectedHidden)
    {
        $scheduledVisibilityService = $this->getScheduledVisibilityService();
        $content = $this->createContent($configuration['publish_from'], $configuration['publish_to']);
        $handler = $this->getContentHandler();
        if ($scheduledVisibilityService->shouldBeHidden($content) && !$handler->isHidden($content)) {
            $handler->hide($content);
        }
        if ($scheduledVisibilityService->shouldBeVisible($content) && $handler->isHidden($content)) {
            $handler->reveal($content);
        }
        $content = $this->getRepository()->getContentService()->loadContent($content->contentInfo->id);
        self::assertEquals($handler->isHidden($content), $expectedHidden);
    }

    private function getContentHandler(): Content
    {
        $repository = $this->getRepository();

        return new Content($repository, $repository->getContentService());
    }
}
