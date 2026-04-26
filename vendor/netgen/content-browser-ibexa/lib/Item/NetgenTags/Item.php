<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Ibexa\Item\NetgenTags;

use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;

final class Item implements ItemInterface, LocationInterface, NetgenTagsInterface
{
    public int $locationId {
        get => $this->tag->id;
    }

    public int $value {
        get => $this->tag->id;
    }

    public ?int $parentId {
        get => $this->tag->parentTagId !== 0 ? $this->tag->parentTagId : null;
    }

    public true $isVisible {
        get => true;
    }

    public bool $isSelectable {
        get => $this->tag->id !== 0;
    }

    public function __construct(
        public private(set) Tag $tag,
        public private(set) string $name,
    ) {}
}
