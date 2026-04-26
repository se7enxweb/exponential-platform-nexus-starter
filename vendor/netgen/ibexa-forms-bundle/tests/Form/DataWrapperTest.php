<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaFormsBundle\Tests\Form;

use Netgen\Bundle\IbexaFormsBundle\Form\DataWrapper;
use PHPUnit\Framework\TestCase;
use stdClass;

final class DataWrapperTest extends TestCase
{
    public function testSetValuesCorrectly(): void
    {
        $payload = new stdClass();
        $payload->payload = 'payload';

        $definition = new stdClass();
        $definition->definition = 'definition';

        $target = new stdClass();
        $target->target = 'target';

        $dataWrapper = new DataWrapper($payload, $definition, $target);

        self::assertSame($payload, $dataWrapper->payload);
        self::assertSame($definition, $dataWrapper->definition);
        self::assertSame($target, $dataWrapper->target);
    }
}
