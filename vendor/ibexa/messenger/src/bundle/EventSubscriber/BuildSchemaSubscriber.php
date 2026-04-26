<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Messenger\EventSubscriber;

use Ibexa\Contracts\DoctrineSchema\Event\SchemaBuilderEvent;
use Ibexa\Contracts\DoctrineSchema\SchemaBuilderEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class BuildSchemaSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            SchemaBuilderEvents::BUILD_SCHEMA => ['onBuildSchema', 200],
        ];
    }

    public function onBuildSchema(SchemaBuilderEvent $event): void
    {
        $event->getSchemaBuilder()->importSchemaFromFile(
            __DIR__ . '/../Resources/config/schema.yaml'
        );
    }
}
