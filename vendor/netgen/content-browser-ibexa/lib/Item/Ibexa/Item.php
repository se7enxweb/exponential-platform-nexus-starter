<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Ibexa\Item\Ibexa;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;

final class Item implements ItemInterface, LocationInterface, IbexaInterface
{
    public private(set) Content $content;

    public int $locationId {
        get => $this->location->id;
    }

    public string $name {
        get => $this->content->getName() ?? '';
    }

    public ?int $parentId {
        get {
            $parentId = $this->location->parentLocationId;

            return $parentId !== 1 ? $parentId : null;
        }
    }

    public bool $isVisible {
        get => !$this->location->invisible;
    }

    public function __construct(
        public private(set) Location $location,
        public private(set) int $value,
        public private(set) bool $isSelectable = true,
    ) {
        $this->content = $this->location->getContent();
    }
}
