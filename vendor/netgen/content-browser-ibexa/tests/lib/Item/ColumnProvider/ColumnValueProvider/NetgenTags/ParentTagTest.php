<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Ibexa\Tests\Item\ColumnProvider\ColumnValueProvider\NetgenTags;

use Ibexa\Core\Helper\TranslationHelper;
use Netgen\ContentBrowser\Ibexa\Item\ColumnProvider\ColumnValueProvider\NetgenTags\ParentTag;
use Netgen\ContentBrowser\Ibexa\Item\NetgenTags\Item;
use Netgen\ContentBrowser\Ibexa\Tests\Stubs\Item as StubItem;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;
use Netgen\TagsBundle\Core\Repository\TagsService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(ParentTag::class)]
final class ParentTagTest extends TestCase
{
    private Stub&TagsService $tagsServiceStub;

    private Stub&TranslationHelper $translationHelperStub;

    private ParentTag $provider;

    protected function setUp(): void
    {
        $this->tagsServiceStub = self::createStub(TagsService::class);
        $this->translationHelperStub = self::createStub(TranslationHelper::class);

        $this->tagsServiceStub
            ->method('sudo')
            ->willReturnCallback(
                fn (callable $callback): mixed => $callback($this->tagsServiceStub),
            );

        $this->provider = new ParentTag(
            $this->tagsServiceStub,
            $this->translationHelperStub,
        );
    }

    public function testGetValue(): void
    {
        $item = new Item(
            new Tag(
                [
                    'parentTagId' => 42,
                ],
            ),
            'Name',
        );

        $parentTag = new Tag(['keywords' => ['eng-GB', 'Parent tag']]);

        $this->tagsServiceStub
            ->method('loadTag')
            ->willReturn($parentTag);

        $this->translationHelperStub
            ->method('getTranslatedByMethod')
            ->willReturn('Parent tag');

        self::assertSame(
            'Parent tag',
            $this->provider->getValue($item),
        );
    }

    public function testGetValueWithNoParentTag(): void
    {
        $item = new Item(
            new Tag(
                [
                    'parentTagId' => 0,
                ],
            ),
            'Name',
        );

        self::assertSame(
            '(No parent)',
            $this->provider->getValue($item),
        );
    }

    public function testGetValueWithInvalidItem(): void
    {
        self::assertNull($this->provider->getValue(new StubItem(42)));
    }
}
