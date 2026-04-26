<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\SiteApi\Tests\Item\ValueUrlGenerator;

use Netgen\Layouts\Ibexa\SiteApi\Item\ValueUrlGenerator\LocationValueUrlGenerator;
use Netgen\Layouts\Ibexa\SiteApi\Tests\Stubs\ContentInfo;
use Netgen\Layouts\Ibexa\SiteApi\Tests\Stubs\Location;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[CoversClass(LocationValueUrlGenerator::class)]
final class LocationValueUrlGeneratorTest extends TestCase
{
    private Stub&UrlGeneratorInterface $urlGeneratorStub;

    private LocationValueUrlGenerator $urlGenerator;

    protected function setUp(): void
    {
        $this->urlGeneratorStub = self::createStub(UrlGeneratorInterface::class);

        $this->urlGenerator = new LocationValueUrlGenerator($this->urlGeneratorStub);
    }

    public function testGenerateDefaultUrl(): void
    {
        $location = new Location(
            [
                'id' => 42,
            ],
        );

        $this->urlGeneratorStub
            ->method('generate')
            ->willReturn('/location/path');

        self::assertSame('/location/path', $this->urlGenerator->generateDefaultUrl($location));
    }

    public function testGenerateAdminUrl(): void
    {
        $location = new Location(
            [
                'id' => 42,
                'contentInfo' => new ContentInfo(['id' => 24]),
            ],
        );

        $this->urlGeneratorStub
            ->method('generate')
            ->willReturn('/admin/location/path');

        self::assertSame('/admin/location/path', $this->urlGenerator->generateAdminUrl($location));
    }
}
