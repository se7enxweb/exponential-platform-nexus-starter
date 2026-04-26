<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Ibexa\Tests\Item\ColumnProvider\ColumnValueProvider\Ibexa;

use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\Location;
use Netgen\ContentBrowser\Ibexa\Item\ColumnProvider\ColumnValueProvider\Ibexa\Visible;
use Netgen\ContentBrowser\Ibexa\Item\Ibexa\Item;
use Netgen\ContentBrowser\Ibexa\Tests\Stubs\Item as StubItem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

#[CoversClass(Visible::class)]
final class VisibleTest extends TestCase
{
    private Stub&TranslatorInterface $translatorStub;

    private Visible $provider;

    protected function setUp(): void
    {
        $this->translatorStub = self::createStub(TranslatorInterface::class);

        $this->provider = new Visible($this->translatorStub);
    }

    public function testGetValueWithVisibleLocation(): void
    {
        $this->translatorStub
            ->method('trans')
            ->willReturn('Yes');

        $item = new Item(
            new Location(
                [
                    'content' => new Content(),
                    'invisible' => false,
                ],
            ),
            24,
        );

        self::assertSame(
            'Yes',
            $this->provider->getValue($item),
        );
    }

    public function testGetValueWithInvisibleLocation(): void
    {
        $this->translatorStub
            ->method('trans')
            ->willReturn('No');

        $item = new Item(
            new Location(
                [
                    'content' => new Content(),
                    'invisible' => true,
                ],
            ),
            24,
        );

        self::assertSame(
            'No',
            $this->provider->getValue($item),
        );
    }

    public function testGetValueWithInvalidItem(): void
    {
        self::assertNull($this->provider->getValue(new StubItem(42)));
    }
}
