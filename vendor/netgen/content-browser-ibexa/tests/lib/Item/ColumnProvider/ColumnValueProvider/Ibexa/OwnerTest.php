<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Ibexa\Tests\Item\ColumnProvider\ColumnValueProvider\Ibexa;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\Repository\Repository;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Netgen\ContentBrowser\Ibexa\Item\ColumnProvider\ColumnValueProvider\Ibexa\Owner;
use Netgen\ContentBrowser\Ibexa\Item\Ibexa\Item;
use Netgen\ContentBrowser\Ibexa\Tests\Stubs\Item as StubItem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(Owner::class)]
final class OwnerTest extends TestCase
{
    private Stub&ContentService $contentServiceStub;

    private Owner $provider;

    protected function setUp(): void
    {
        $this->contentServiceStub = self::createStub(ContentService::class);

        $repositoryStub = self::createStub(Repository::class);
        $repositoryStub
            ->method('sudo')
            ->willReturnCallback(
                static fn (callable $callback): mixed => $callback($repositoryStub),
            );

        $repositoryStub
            ->method('getContentService')
            ->willReturn($this->contentServiceStub);

        $this->provider = new Owner($repositoryStub);
    }

    public function testGetValue(): void
    {
        $content = new Content(
            [
                'versionInfo' => new VersionInfo(
                    [
                        'contentInfo' => new ContentInfo(
                            [
                                'ownerId' => 42,
                            ],
                        ),
                    ],
                ),
            ],
        );

        $item = new Item(
            new Location(['content' => $content]),
            24,
        );

        $ownerContent = new Content(
            [
                'versionInfo' => new VersionInfo(
                    [
                        'prioritizedNameLanguageCode' => 'eng-GB',
                        'names' => ['eng-GB' => 'Owner name'],
                    ],
                ),
            ],
        );

        $this->contentServiceStub
            ->method('loadContent')
            ->willReturn($ownerContent);

        self::assertSame(
            'Owner name',
            $this->provider->getValue($item),
        );
    }

    public function testGetValueWithNonExistingOwner(): void
    {
        $content = new Content(
            [
                'versionInfo' => new VersionInfo(
                    [
                        'contentInfo' => new ContentInfo(
                            [
                                'ownerId' => 42,
                            ],
                        ),
                    ],
                ),
            ],
        );

        $item = new Item(
            new Location(['content' => $content]),
            24,
        );

        $this->contentServiceStub
            ->method('loadContent')
            ->willThrowException(new NotFoundException('user', 42));

        self::assertSame('', $this->provider->getValue($item));
    }

    public function testGetValueWithInvalidItem(): void
    {
        self::assertNull($this->provider->getValue(new StubItem(42)));
    }
}
