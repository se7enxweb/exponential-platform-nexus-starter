<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Ibexa\Tests\Backend;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\Base\Exceptions\NotFoundException as IbexaNotFoundException;
use Ibexa\Core\Helper\TranslationHelper;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Ibexa\Backend\NetgenTagsBackend;
use Netgen\ContentBrowser\Ibexa\Item\NetgenTags\Item;
use Netgen\ContentBrowser\Ibexa\Item\NetgenTags\NetgenTagsInterface;
use Netgen\ContentBrowser\Ibexa\Tests\Stubs\Location as StubLocation;
use Netgen\TagsBundle\API\Repository\TagsService;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;
use Netgen\TagsBundle\API\Repository\Values\Tags\TagList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(NetgenTagsBackend::class)]
final class NetgenTagsBackendTest extends TestCase
{
    private Stub&TagsService $tagsServiceStub;

    private NetgenTagsBackend $backend;

    protected function setUp(): void
    {
        $this->tagsServiceStub = self::createStub(TagsService::class);

        $configResolverStub = self::createStub(ConfigResolverInterface::class);

        $configResolverStub
            ->method('getParameter')
            ->willReturn(['eng-GB', 'cro-HR']);

        $this->backend = new NetgenTagsBackend(
            $this->tagsServiceStub,
            self::createStub(TranslationHelper::class),
            $configResolverStub,
        );
    }

    public function testGetSections(): void
    {
        $locations = [...$this->backend->getSections()];

        self::assertCount(1, $locations);

        $location = $locations[0];

        self::assertInstanceOf(NetgenTagsInterface::class, $location);
    }

    public function testLoadLocation(): void
    {
        $this->tagsServiceStub
            ->method('loadTag')
            ->willReturn($this->getTag(1));

        $location = $this->backend->loadLocation(1);

        self::assertSame(1, $location->locationId);
    }

    public function testLoadLocationThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Item with value "1" not found.');

        $this->tagsServiceStub
            ->method('loadTag')
            ->willThrowException(new IbexaNotFoundException('tag', 1));

        $this->backend->loadLocation(1);
    }

    public function testLoadItem(): void
    {
        $this->tagsServiceStub
            ->method('loadTag')
            ->willReturn($this->getTag(1));

        $item = $this->backend->loadItem(1);

        self::assertSame(1, $item->value);
    }

    public function testLoadItemThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Item with value "1" not found.');

        $this->tagsServiceStub
            ->method('loadTag')
            ->willThrowException(new IbexaNotFoundException('tag', 1));

        $this->backend->loadItem(1);
    }

    public function testGetSubLocations(): void
    {
        $tag = $this->getTag(1);

        $this->tagsServiceStub
            ->method('loadTagChildren')
            ->willReturn(
                new TagList([$this->getTag(0, 1), $this->getTag(0, 1)]),
            );

        $locations = [];

        foreach ($this->backend->getSubLocations(new Item($tag, 'tag')) as $location) {
            self::assertInstanceOf(Item::class, $location);
            self::assertSame(1, $location->parentId);

            $locations[] = $location;
        }

        self::assertCount(2, $locations);
    }

    public function testGetSubLocationsWithInvalidItem(): void
    {
        $locations = $this->backend->getSubLocations(new StubLocation(0));

        self::assertIsArray($locations);
        self::assertEmpty($locations);
    }

    public function testGetSubLocationsCount(): void
    {
        $tag = $this->getTag(1);

        $this->tagsServiceStub
            ->method('getTagChildrenCount')
            ->willReturn(2);

        $count = $this->backend->getSubLocationsCount(new Item($tag, 'tag'));

        self::assertSame(2, $count);
    }

    public function testGetSubLocationsCountWithInvalidItem(): void
    {
        $count = $this->backend->getSubLocationsCount(new StubLocation(0));

        self::assertSame(0, $count);
    }

    public function testGetSubItems(): void
    {
        $tag = $this->getTag(1);

        $this->tagsServiceStub
            ->method('loadTagChildren')
            ->willReturn(
                new TagList([$this->getTag(0, 1), $this->getTag(0, 1)]),
            );

        $items = [];

        foreach ($this->backend->getSubItems(new Item($tag, 'tag')) as $item) {
            self::assertInstanceOf(Item::class, $item);
            self::assertSame(1, $item->parentId);

            $items[] = $item;
        }

        self::assertCount(2, $items);
    }

    public function testGetSubItemsWithOffsetAndLimit(): void
    {
        $tag = $this->getTag(1);

        $this->tagsServiceStub
            ->method('loadTagChildren')
            ->willReturn(new TagList([$this->getTag(0, 1), $this->getTag(0, 1)]));

        $items = [];

        foreach ($this->backend->getSubItems(new Item($tag, 'tag'), 5, 10) as $item) {
            self::assertInstanceOf(Item::class, $item);
            self::assertSame(1, $item->parentId);

            $items[] = $item;
        }

        self::assertCount(2, $items);
    }

    public function testGetSubItemsWithInvalidItem(): void
    {
        $locations = $this->backend->getSubItems(new StubLocation(0));

        self::assertIsArray($locations);
        self::assertEmpty($locations);
    }

    public function testGetSubItemsCount(): void
    {
        $tag = $this->getTag(1);

        $this->tagsServiceStub
            ->method('getTagChildrenCount')
            ->willReturn(2);

        $count = $this->backend->getSubItemsCount(new Item($tag, 'tag'));

        self::assertSame(2, $count);
    }

    public function testGetSubItemsCountWithInvalidItem(): void
    {
        $count = $this->backend->getSubItemsCount(new StubLocation(0));

        self::assertSame(0, $count);
    }

    /**
     * Returns the tag object used in tests.
     */
    private function getTag(int $id = 0, int $parentTagId = 0): Tag
    {
        return new Tag(
            [
                'id' => $id,
                'parentTagId' => $parentTagId,
            ],
        );
    }
}
