<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\SiteApi\Tests\Item\ValueLoader;

use Exception;
use Netgen\IbexaSiteApi\API\LoadService;
use Netgen\Layouts\Ibexa\SiteApi\Item\ValueLoader\ContentValueLoader;
use Netgen\Layouts\Ibexa\SiteApi\Tests\Stubs\Content;
use Netgen\Layouts\Ibexa\SiteApi\Tests\Stubs\ContentInfo;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(ContentValueLoader::class)]
final class ContentValueLoaderTest extends TestCase
{
    private Stub&LoadService $loadServiceStub;

    private ContentValueLoader $valueLoader;

    protected function setUp(): void
    {
        $this->loadServiceStub = self::createStub(LoadService::class);

        $this->valueLoader = new ContentValueLoader($this->loadServiceStub);
    }

    public function testLoad(): void
    {
        $contentInfo = new ContentInfo(
            [
                'id' => 52,
                'published' => true,
                'mainLocationId' => 42,
            ],
        );

        $content = new Content(
            [
                'contentInfo' => $contentInfo,
            ],
        );

        $this->loadServiceStub
            ->method('loadContent')
            ->willReturn($content);

        self::assertSame($contentInfo, $this->valueLoader->load(52));
    }

    public function testLoadWithNoContent(): void
    {
        $this->loadServiceStub
            ->method('loadContent')
            ->willThrowException(new Exception());

        self::assertNull($this->valueLoader->load(52));
    }

    public function testLoadWithNonPublishedContent(): void
    {
        $contentInfo = new ContentInfo(
            [
                'published' => false,
                'mainLocationId' => 42,
            ],
        );

        $content = new Content(
            [
                'contentInfo' => $contentInfo,
            ],
        );

        $this->loadServiceStub
            ->method('loadContent')
            ->willReturn($content);

        self::assertNull($this->valueLoader->load(52));
    }

    public function testLoadWithNoMainLocation(): void
    {
        $contentInfo = new ContentInfo(
            [
                'published' => true,
                'mainLocationId' => null,
            ],
        );

        $content = new Content(
            [
                'contentInfo' => $contentInfo,
            ],
        );

        $this->loadServiceStub
            ->method('loadContent')
            ->willReturn($content);

        self::assertNull($this->valueLoader->load(52));
    }

    public function testLoadByRemoteId(): void
    {
        $contentInfo = new ContentInfo(
            [
                'remoteId' => 'abc',
                'published' => true,
                'mainLocationId' => 42,
            ],
        );

        $content = new Content(
            [
                'contentInfo' => $contentInfo,
            ],
        );

        $this->loadServiceStub
            ->method('loadContentByRemoteId')
            ->willReturn($content);

        self::assertSame($contentInfo, $this->valueLoader->loadByRemoteId('abc'));
    }

    public function testLoadByRemoteIdWithNoContent(): void
    {
        $this->loadServiceStub
            ->method('loadContentByRemoteId')
            ->willThrowException(new Exception());

        self::assertNull($this->valueLoader->loadByRemoteId('abc'));
    }

    public function testLoadByRemoteIdWithNonPublishedContent(): void
    {
        $contentInfo = new ContentInfo(
            [
                'published' => false,
                'mainLocationId' => 42,
            ],
        );

        $content = new Content(
            [
                'contentInfo' => $contentInfo,
            ],
        );

        $this->loadServiceStub
            ->method('loadContentByRemoteId')
            ->willReturn($content);

        self::assertNull($this->valueLoader->loadByRemoteId('abc'));
    }

    public function testLoadByRemoteIdWithNoMainLocation(): void
    {
        $contentInfo = new ContentInfo(
            [
                'published' => true,
                'mainLocationId' => null,
            ],
        );

        $content = new Content(
            [
                'contentInfo' => $contentInfo,
            ],
        );

        $this->loadServiceStub
            ->method('loadContentByRemoteId')
            ->willReturn($content);

        self::assertNull($this->valueLoader->loadByRemoteId('abc'));
    }
}
