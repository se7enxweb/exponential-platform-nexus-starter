<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Ibexa\Item\NetgenTags;

use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;

interface NetgenTagsInterface
{
    /**
     * Returns the tag.
     */
    public Tag $tag { get; }
}
