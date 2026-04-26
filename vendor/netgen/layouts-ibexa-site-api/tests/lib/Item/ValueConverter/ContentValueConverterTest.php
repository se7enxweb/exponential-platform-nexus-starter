<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\SiteApi\Tests\Item\ValueConverter;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo as IbexaContentInfo;
use Netgen\IbexaSiteApi\API\LoadService;
use Netgen\Layouts\Ibexa\SiteApi\Item\ValueConverter\ContentValueConverter;
use Netgen\Layouts\Ibexa\SiteApi\Tests\Stubs\Content;
use Netgen\Layouts\Ibexa\SiteApi\Tests\Stubs\ContentInfo;
use Netgen\Layouts\Ibexa\SiteApi\Tests\Stubs\Location;
use Netgen\Layouts\Item\ValueConverterInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(ContentValueConverter::class)]
final class ContentValueConverterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\Stub&\Netgen\Layouts\Item\ValueConverterInterface<\Netgen\IbexaSiteApi\API\Values\Content>
     */
    private Stub&ValueConverterInterface $innerConverterStub;

    private Stub&LoadService $loadServiceStub;

    private ContentValueConverter $valueConverter;

    protected function setUp(): void
    {
        $this->innerConverterStub = self::createStub(ValueConverterInterface::class);
        $this->loadServiceStub = self::createStub(LoadService::class);

        $this->valueConverter = new ContentValueConverter(
            $this->innerConverterStub,
            $this->loadServiceStub,
        );
    }

    public function testSupports(): void
    {
        self::assertTrue($this->valueConverter->supports(new ContentInfo()));
    }

    public function testSupportsWithoutSiteApiContentInfo(): void
    {
        $contentInfo = new IbexaContentInfo();

        $this->innerConverterStub
            ->method('supports')
            ->willReturn(true);

        self::assertTrue($this->valueConverter->supports($contentInfo));
    }

    public function testGetValueType(): void
    {
        self::assertSame(
            'ibexa_content',
            $this->valueConverter->getValueType(
                new ContentInfo(),
            ),
        );
    }

    public function testGetId(): void
    {
        self::assertSame(
            24,
            $this->valueConverter->getId(
                new ContentInfo(['id' => 24]),
            ),
        );
    }

    public function testGetIdWithoutSiteApiContentInfo(): void
    {
        $contentInfo = new IbexaContentInfo();

        $this->innerConverterStub
            ->method('getId')
            ->willReturn(42);

        self::assertSame(42, $this->valueConverter->getId($contentInfo));
    }

    public function testGetRemoteId(): void
    {
        self::assertSame(
            'abc',
            $this->valueConverter->getRemoteId(
                new ContentInfo(['remoteId' => 'abc']),
            ),
        );
    }

    public function testGetRemoteIdWithoutSiteApiContentInfo(): void
    {
        $contentInfo = new IbexaContentInfo();

        $this->innerConverterStub
            ->method('getRemoteId')
            ->willReturn('abc');

        self::assertSame('abc', $this->valueConverter->getRemoteId($contentInfo));
    }

    public function testGetName(): void
    {
        self::assertSame(
            'Cool name',
            $this->valueConverter->getName(
                new ContentInfo(['name' => 'Cool name']),
            ),
        );
    }

    public function testGetNameWithoutSiteApiContentInfo(): void
    {
        $contentInfo = new IbexaContentInfo();

        $this->innerConverterStub
            ->method('getName')
            ->willReturn('Cool name');

        self::assertSame('Cool name', $this->valueConverter->getName($contentInfo));
    }

    public function testGetIsVisible(): void
    {
        self::assertTrue(
            $this->valueConverter->getIsVisible(
                new ContentInfo(['mainLocation' => new Location(['invisible' => false])]),
            ),
        );
    }

    public function testGetIsVisibleWithoutMainLocation(): void
    {
        self::assertFalse(
            $this->valueConverter->getIsVisible(
                new ContentInfo(['mainLocation' => new Location(['invisible' => true])]),
            ),
        );
    }

    public function testGetIsVisibleWithoutSiteApiContentInfo(): void
    {
        $contentInfo = new IbexaContentInfo();

        $this->innerConverterStub
            ->method('getIsVisible')
            ->willReturn(true);

        self::assertTrue($this->valueConverter->getIsVisible($contentInfo));
    }

    public function testGetObject(): void
    {
        $object = new ContentInfo(['id' => 42]);

        self::assertSame($object, $this->valueConverter->getObject($object));
    }

    public function testGetObjectWithoutSiteApiContentInfo(): void
    {
        $contentInfo = new ContentInfo();
        $content = new Content(['contentInfo' => $contentInfo]);

        $this->loadServiceStub
            ->method('loadContent')
            ->willReturn($content);

        self::assertSame($contentInfo, $this->valueConverter->getObject(new IbexaContentInfo(['id' => 42])));
    }
}
