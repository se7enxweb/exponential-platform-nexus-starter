<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\SiteApi\Tests\Item\ValueLoader;

use Exception;
use Netgen\IbexaSiteApi\API\LoadService;
use Netgen\Layouts\Ibexa\SiteApi\Item\ValueLoader\LocationValueLoader;
use Netgen\Layouts\Ibexa\SiteApi\Tests\Stubs\ContentInfo;
use Netgen\Layouts\Ibexa\SiteApi\Tests\Stubs\Location;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(LocationValueLoader::class)]
final class LocationValueLoaderTest extends TestCase
{
    private Stub&LoadService $loadServiceStub;

    private LocationValueLoader $valueLoader;

    protected function setUp(): void
    {
        $this->loadServiceStub = self::createStub(LoadService::class);

        $this->valueLoader = new LocationValueLoader($this->loadServiceStub);
    }

    public function testLoad(): void
    {
        $location = new Location(
            [
                'id' => 52,
                'contentInfo' => new ContentInfo(
                    [
                        'published' => true,
                    ],
                ),
            ],
        );

        $this->loadServiceStub
            ->method('loadLocation')
            ->willReturn($location);

        self::assertSame($location, $this->valueLoader->load(52));
    }

    public function testLoadWithNoLocation(): void
    {
        $this->loadServiceStub
            ->method('loadLocation')
            ->willThrowException(new Exception());

        self::assertNull($this->valueLoader->load(52));
    }

    public function testLoadWithNonPublishedContent(): void
    {
        $location = new Location(
            [
                'contentInfo' => new ContentInfo(
                    [
                        'published' => false,
                    ],
                ),
            ],
        );

        $this->loadServiceStub
            ->method('loadLocation')
            ->willReturn($location);

        self::assertNull($this->valueLoader->load(52));
    }

    public function testLoadByRemoteId(): void
    {
        $location = new Location(
            [
                'remoteId' => 'abc',
                'contentInfo' => new ContentInfo(
                    [
                        'published' => true,
                    ],
                ),
            ],
        );

        $this->loadServiceStub
            ->method('loadLocationByRemoteId')
            ->willReturn($location);

        self::assertSame($location, $this->valueLoader->loadByRemoteId('abc'));
    }

    public function testLoadByRemoteIdWithNoLocation(): void
    {
        $this->loadServiceStub
            ->method('loadLocationByRemoteId')
            ->willThrowException(new Exception());

        self::assertNull($this->valueLoader->loadByRemoteId('abc'));
    }

    public function testLoadByRemoteIdWithNonPublishedContent(): void
    {
        $location = new Location(
            [
                'contentInfo' => new ContentInfo(
                    [
                        'published' => false,
                    ],
                ),
            ],
        );

        $this->loadServiceStub
            ->method('loadLocationByRemoteId')
            ->willReturn($location);

        self::assertNull($this->valueLoader->loadByRemoteId('abc'));
    }
}
