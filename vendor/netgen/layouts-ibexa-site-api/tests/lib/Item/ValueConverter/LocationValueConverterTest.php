<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\SiteApi\Tests\Item\ValueConverter;

use Ibexa\Core\Repository\Values\Content\Location as IbexaLocation;
use Netgen\IbexaSiteApi\API\LoadService;
use Netgen\Layouts\Ibexa\SiteApi\Item\ValueConverter\LocationValueConverter;
use Netgen\Layouts\Ibexa\SiteApi\Tests\Stubs\ContentInfo;
use Netgen\Layouts\Ibexa\SiteApi\Tests\Stubs\Location;
use Netgen\Layouts\Item\ValueConverterInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(LocationValueConverter::class)]
final class LocationValueConverterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\Stub&\Netgen\Layouts\Item\ValueConverterInterface<\Netgen\IbexaSiteApi\API\Values\Location>
     */
    private Stub&ValueConverterInterface $innerConverterStub;

    private Stub&LoadService $loadServiceStub;

    private LocationValueConverter $valueConverter;

    protected function setUp(): void
    {
        $this->innerConverterStub = self::createStub(ValueConverterInterface::class);
        $this->loadServiceStub = self::createStub(LoadService::class);

        $this->valueConverter = new LocationValueConverter(
            $this->innerConverterStub,
            $this->loadServiceStub,
        );
    }

    public function testSupports(): void
    {
        self::assertTrue($this->valueConverter->supports(new Location()));
    }

    public function testSupportsWithoutSiteApiLocation(): void
    {
        $location = new IbexaLocation();

        $this->innerConverterStub
            ->method('supports')
            ->willReturn(true);

        self::assertTrue($this->valueConverter->supports($location));
    }

    public function testGetValueType(): void
    {
        self::assertSame(
            'ibexa_location',
            $this->valueConverter->getValueType(
                new Location(),
            ),
        );
    }

    public function testGetId(): void
    {
        self::assertSame(
            24,
            $this->valueConverter->getId(
                new Location(['id' => 24]),
            ),
        );
    }

    public function testGetIdWithoutSiteApiLocation(): void
    {
        $location = new IbexaLocation();

        $this->innerConverterStub
            ->method('getId')
            ->willReturn(42);

        self::assertSame(42, $this->valueConverter->getId($location));
    }

    public function testGetRemoteId(): void
    {
        self::assertSame(
            'abc',
            $this->valueConverter->getRemoteId(
                new Location(['remoteId' => 'abc']),
            ),
        );
    }

    public function testGetRemoteIdWithoutSiteApiLocation(): void
    {
        $location = new IbexaLocation();

        $this->innerConverterStub
            ->method('getRemoteId')
            ->willReturn('abc');

        self::assertSame('abc', $this->valueConverter->getRemoteId($location));
    }

    public function testGetName(): void
    {
        self::assertSame(
            'Cool name',
            $this->valueConverter->getName(
                new Location(['contentInfo' => new ContentInfo(['name' => 'Cool name'])]),
            ),
        );
    }

    public function testGetNameWithoutSiteApiLocation(): void
    {
        $location = new IbexaLocation();

        $this->innerConverterStub
            ->method('getName')
            ->willReturn('Cool name');

        self::assertSame('Cool name', $this->valueConverter->getName($location));
    }

    public function testGetIsVisible(): void
    {
        self::assertTrue(
            $this->valueConverter->getIsVisible(
                new Location(['invisible' => false]),
            ),
        );
    }

    public function testGetIsVisibleWithoutSiteApiLocation(): void
    {
        $location = new IbexaLocation();

        $this->innerConverterStub
            ->method('getIsVisible')
            ->willReturn(true);

        self::assertTrue($this->valueConverter->getIsVisible($location));
    }

    public function testGetObject(): void
    {
        $object = new Location(['id' => 42]);

        self::assertSame($object, $this->valueConverter->getObject($object));
    }

    public function testGetObjectWithoutSiteApiLocation(): void
    {
        $location = new Location();

        $this->loadServiceStub
            ->method('loadLocation')
            ->willReturn($location);

        self::assertSame($location, $this->valueConverter->getObject(new IbexaLocation(['id' => 42])));
    }
}
