<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Messenger\Serializer\Normalizer;

use Ibexa\Bundle\Messenger\Serializer\Normalizer\LockKeyNormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Lock\Key;

final class LockKeyNormalizerTest extends TestCase
{
    private LockKeyNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new LockKeyNormalizer();
    }

    /**
     * @dataProvider provideForTest
     *
     * @param array{
     *     resource: string,
     *     expiringTime: float|null,
     *     state: array<string, mixed>,
     * } $expected
     */
    public function testNormalize(Key $data, array $expected): void
    {
        $normalized = $this->normalizer->normalize($data);

        self::assertSame($expected, $normalized);
    }

    /**
     * @dataProvider provideForTest
     *
     * @param array{
     *     resource: string,
     *     expiringTime: float|null,
     *     state: array<string, mixed>,
     * } $data
     */
    public function testDenormalize(Key $expectedKey, array $data): void
    {
        $denormalized = $this->normalizer->denormalize($data, Key::class);

        self::assertSame($data['resource'], (string)$denormalized);

        if ($data['expiringTime'] === null) {
            self::assertNull($denormalized->getRemainingLifetime());
        } else {
            // Some time will pass
            self::assertNotNull($denormalized->getRemainingLifetime());
            self::assertLessThan($data['expiringTime'], $denormalized->getRemainingLifetime());
        }
        self::assertFalse($denormalized->hasState('_non_existent_state_'));

        foreach ($data['state'] as $key => $value) {
            self::assertTrue($denormalized->hasState($key));
            self::assertSame($value, $denormalized->getState($key));
        }
    }

    /**
     * @return iterable<array{
     *     \Symfony\Component\Lock\Key,
     *     array{
     *         resource: string,
     *         expiringTime: float|null,
     *         state: array<string, mixed>,
     *     },
     * }>
     */
    public static function provideForTest(): iterable
    {
        $currentTime = time();
        $key = new Key('bar');
        \Closure::bind(function () use ($currentTime): void {
            $this->expiringTime = $currentTime + 300;
        }, $key, Key::class)();

        yield [
            $key,
            [
                'resource' => 'bar',
                'expiringTime' => (float)($currentTime + 300),
                'state' => [],
            ],
        ];

        $key = new Key('foo');
        $key->setState('foo', 'bar');

        yield [
            $key,
            [
                'resource' => 'foo',
                'expiringTime' => null,
                'state' => [
                    'foo' => 'bar',
                ],
            ],
        ];
    }
}
