<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaScheduledVisibilityBundle\Core;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;

abstract class VisibilityHandler
{
    abstract public function hide(Content $content): void;

    abstract public function reveal(Content $content): void;

    abstract public function isHidden(Content $content): bool;

    abstract public function isVisible(Content $content): bool;
}
