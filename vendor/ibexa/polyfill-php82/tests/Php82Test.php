<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\PolyfillPhp82;

use Generator;
use function Ibexa\PolyfillPhp82\iterator_to_array;
use PHPUnit\Framework\TestCase;

final class Php82Test extends TestCase
{
    public function testGeneratorForIteratorToArray(): void
    {
        $generator = static function (): Generator {
            yield 'foo' => 'bar';
            yield 'bar' => 'foo';
        };
        $result = iterator_to_array($generator());

        self::assertSame([
            'foo' => 'bar',
            'bar' => 'foo',
        ], $result);
    }

    public function testArrayForIteratorToArray(): void
    {
        $result = iterator_to_array([
            'foo' => 'bar',
            'bar' => 'foo',
        ]);

        self::assertSame([
            'foo' => 'bar',
            'bar' => 'foo',
        ], $result);
    }
}
