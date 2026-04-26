<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\SiteApi\Tests\Parameters\ValueObjectProvider;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Netgen\IbexaSiteApi\API\LoadService;
use Netgen\Layouts\Error\ErrorHandlerInterface;
use Netgen\Layouts\Ibexa\SiteApi\Parameters\ValueObjectProvider\LocationProvider;
use Netgen\Layouts\Ibexa\SiteApi\Tests\Stubs\Location;
use Netgen\Layouts\Parameters\ValueObjectProviderInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(LocationProvider::class)]
final class LocationProviderTest extends TestCase
{
    private Stub&LoadService $loadServiceStub;

    private ValueObjectProviderInterface $valueObjectProvider;

    protected function setUp(): void
    {
        $this->loadServiceStub = self::createStub(LoadService::class);

        $repositoryStub = self::createStub(Repository::class);
        $repositoryStub
            ->method('sudo')
            ->willReturnCallback(
                static fn (callable $callback): mixed => $callback($repositoryStub),
            );

        $this->valueObjectProvider = new LocationProvider(
            $repositoryStub,
            $this->loadServiceStub,
            self::createStub(ErrorHandlerInterface::class),
        );
    }

    public function testGetValueObject(): void
    {
        $location = new Location();

        $this->loadServiceStub
            ->method('loadLocation')
            ->willReturn($location);

        self::assertSame($location, $this->valueObjectProvider->getValueObject(42));
    }

    public function testGetValueObjectWithNullValue(): void
    {
        self::assertNull($this->valueObjectProvider->getValueObject(null));
    }

    public function testGetValueObjectWithNonExistentLocation(): void
    {
        $this->loadServiceStub
            ->method('loadLocation')
            ->willThrowException(new NotFoundException('location', 42));

        self::assertNull($this->valueObjectProvider->getValueObject(42));
    }
}
