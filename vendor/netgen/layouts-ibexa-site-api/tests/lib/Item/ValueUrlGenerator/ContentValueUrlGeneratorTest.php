<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\SiteApi\Tests\Item\ValueUrlGenerator;

use Netgen\Layouts\Ibexa\SiteApi\Item\ValueUrlGenerator\ContentValueUrlGenerator;
use Netgen\Layouts\Ibexa\SiteApi\Tests\Stubs\ContentInfo;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[CoversClass(ContentValueUrlGenerator::class)]
final class ContentValueUrlGeneratorTest extends TestCase
{
    private Stub&UrlGeneratorInterface $urlGeneratorStub;

    private ContentValueUrlGenerator $urlGenerator;

    protected function setUp(): void
    {
        $this->urlGeneratorStub = self::createStub(UrlGeneratorInterface::class);

        $this->urlGenerator = new ContentValueUrlGenerator($this->urlGeneratorStub);
    }

    public function testGenerateDefaultUrl(): void
    {
        $contentInfo = new ContentInfo(
            [
                'id' => 42,
            ],
        );

        $this->urlGeneratorStub
            ->method('generate')
            ->willReturn('/content/path');

        self::assertSame('/content/path', $this->urlGenerator->generateDefaultUrl($contentInfo));
    }

    public function testGenerateAdminUrl(): void
    {
        $contentInfo = new ContentInfo(
            [
                'id' => 42,
            ],
        );

        $this->urlGeneratorStub
            ->method('generate')
            ->willReturn('/admin/content/path');

        self::assertSame('/admin/content/path', $this->urlGenerator->generateAdminUrl($contentInfo));
    }
}
