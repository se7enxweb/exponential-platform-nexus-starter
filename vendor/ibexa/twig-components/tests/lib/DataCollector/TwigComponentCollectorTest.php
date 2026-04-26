<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\TwigComponents\DataCollector;

use Ibexa\TwigComponents\Component\HtmlComponent;
use Ibexa\TwigComponents\DataCollector\TwigComponentCollector;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class TwigComponentCollectorTest extends TestCase
{
    public function testAddRenderedComponentStoresData(): void
    {
        $component1 = new class('someContent1') extends HtmlComponent {};
        $component2 = new class('someContent2') extends HtmlComponent {};

        $collector = new TwigComponentCollector();
        $collector->addRenderedComponent('group1', 'component_name1', $component1);
        $collector->addRenderedComponent('group2', 'component_name2', $component2);

        $collector->collect(new Request(), new Response());

        self::assertSame(
            [
                ['group' => 'group1', 'name' => 'component_name1', 'componentClass' => HtmlComponent::class],
                ['group' => 'group2', 'name' => 'component_name2', 'componentClass' => HtmlComponent::class],
            ],
            $collector->getRenderedComponents()
        );
    }

    public function testAddAvailableGroupsStoresData(): void
    {
        $collector = new TwigComponentCollector();
        $collector->addAvailableGroups('group1');
        $collector->addAvailableGroups('group2');

        $collector->collect(new Request(), new Response());

        self::assertSame(
            [
                ['group' => 'group1'],
                ['group' => 'group2'],
            ],
            $collector->getAvailableGroups()
        );
    }
}
