<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Ibexa\Tests\Item\NetgenTags;

use Netgen\ContentBrowser\Ibexa\Item\NetgenTags\Item;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Item::class)]
final class ItemTest extends TestCase
{
    private Tag $tag;

    private Item $item;

    protected function setUp(): void
    {
        $this->tag = new Tag(['id' => 42, 'parentTagId' => 24]);

        $this->item = new Item($this->tag, 'Keyword');
    }

    public function testGetLocationId(): void
    {
        self::assertSame(42, $this->item->locationId);
    }

    public function testGetValue(): void
    {
        self::assertSame(42, $this->item->value);
    }

    public function testGetName(): void
    {
        self::assertSame('Keyword', $this->item->name);
    }

    public function testGetParentId(): void
    {
        self::assertSame(24, $this->item->parentId);
    }

    public function testIsSelectable(): void
    {
        self::assertTrue($this->item->isSelectable);
    }

    public function testGetTag(): void
    {
        self::assertSame($this->tag, $this->item->tag);
    }
}
