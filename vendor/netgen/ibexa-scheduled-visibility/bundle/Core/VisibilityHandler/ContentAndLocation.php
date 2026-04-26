<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\VisibilityHandler;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\VisibilityHandler;
use Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\VisibilityHandler\Content as ContentHandler;
use Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\VisibilityHandler\Location as LocationHandler;

final class ContentAndLocation extends VisibilityHandler
{
    public function __construct(
        private readonly ContentHandler $contentHandler,
        private readonly LocationHandler $locationHandler,
    ) {}

    public function hide(Content $content): void
    {
        $this->contentHandler->hide($content);
        $this->locationHandler->hide($content);
    }

    public function reveal(Content $content): void
    {
        $this->contentHandler->reveal($content);
        $this->locationHandler->reveal($content);
    }

    public function isHidden(Content $content): bool
    {
        return $this->contentHandler->isHidden($content) && $this->locationHandler->isHidden($content);
    }

    public function isVisible(Content $content): bool
    {
        return $this->contentHandler->isVisible($content) && $this->locationHandler->isVisible($content);
    }
}
