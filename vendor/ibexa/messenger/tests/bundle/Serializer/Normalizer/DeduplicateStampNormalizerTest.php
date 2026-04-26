<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Messenger\Serializer\Normalizer;

use Ibexa\Bundle\Messenger\Serializer\Normalizer\DeduplicateStampNormalizer;
use Ibexa\Bundle\Messenger\Stamp\DeduplicateStamp;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Lock\Key;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class DeduplicateStampNormalizerTest extends TestCase
{
    private DeduplicateStampNormalizer $normalizer;

    /** @var \Symfony\Component\Serializer\Normalizer\NormalizerInterface&\PHPUnit\Framework\MockObject\MockObject */
    private NormalizerInterface $subNormalizer;

    /** @var \Symfony\Component\Serializer\Normalizer\DenormalizerInterface&\PHPUnit\Framework\MockObject\MockObject */
    private DenormalizerInterface $subDenormalizer;

    protected function setUp(): void
    {
        $this->normalizer = new DeduplicateStampNormalizer();
        $this->subNormalizer = $this->createMock(NormalizerInterface::class);
        $this->subDenormalizer = $this->createMock(DenormalizerInterface::class);

        $this->normalizer->setNormalizer($this->subNormalizer);
        $this->normalizer->setDenormalizer($this->subDenormalizer);
    }

    /**
     * @dataProvider provideForTest
     *
     * @param array{
     *     key: \ArrayObject<string, mixed>,
     *     ttl: float,
     *     only_deduplicate_in_queue: bool,
     * } $expected
     */
    public function testNormalize(DeduplicateStamp $stamp, array $expected): void
    {
        $this->subDenormalizer->expects(self::never())->method(self::anything());

        $this->subNormalizer->expects(self::once())
            ->method('normalize')
            ->with(self::isInstanceOf(Key::class))
            ->willReturn($expected['key']);

        $normalized = $this->normalizer->normalize($stamp);

        self::assertSame($expected, $normalized);
    }

    /**
     * @dataProvider provideForTest
     *
     * @param array{
     *     key: \ArrayObject<string, mixed>,
     *     ttl: float,
     *     only_deduplicate_in_queue: bool,
     * } $data
     */
    public function testDenormalize(DeduplicateStamp $expectedStamp, array $data): void
    {
        $this->subNormalizer->expects(self::never())->method(self::anything());

        $this->subDenormalizer
            ->expects(self::once())
            ->method('denormalize')
            ->with(self::identicalTo($data['key']), Key::class)
            ->willReturn($expectedStamp->getKey());

        $denormalized = $this->normalizer->denormalize($data, DeduplicateStamp::class);

        self::assertSame($expectedStamp->getKey(), $denormalized->getKey());
        self::assertSame($expectedStamp->getTtl(), $denormalized->getTtl());
        self::assertSame($expectedStamp->onlyDeduplicateInQueue(), $denormalized->onlyDeduplicateInQueue());
    }

    /**
     * @return iterable<array{
     *     DeduplicateStamp,
     *     array{
     *         key: \ArrayObject<string, mixed>,
     *         ttl: float,
     *         only_deduplicate_in_queue: bool,
     *     },
     * }>
     */
    public static function provideForTest(): iterable
    {
        yield [
            new DeduplicateStamp('foo_key'),
            [
                'key' => new \ArrayObject(),
                'ttl' => 300.0,
                'only_deduplicate_in_queue' => false,
            ],
        ];

        yield [
            new DeduplicateStamp('foo_key', 600.0, true),
            [
                'key' => new \ArrayObject(),
                'ttl' => 600.0,
                'only_deduplicate_in_queue' => true,
            ],
        ];
    }
}
