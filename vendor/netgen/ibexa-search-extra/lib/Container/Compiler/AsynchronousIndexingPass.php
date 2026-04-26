<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Container\Compiler;

use Ibexa\Core\Search\Common\EventSubscriber\ContentEventSubscriber as CoreContentEventSubscriber;
use Ibexa\Core\Search\Common\EventSubscriber\LocationEventSubscriber as CoreLocationEventSubscriber;
use Ibexa\Core\Search\Common\EventSubscriber\ObjectStateEventSubscriber as CoreObjectStateEventSubscriber;
use Ibexa\Core\Search\Common\EventSubscriber\SectionEventSubscriber as CoreSectionEventSubscriber;
use Ibexa\Core\Search\Common\EventSubscriber\TrashEventSubscriber as CoreTrashEventSubscriber;
use Ibexa\Core\Search\Common\EventSubscriber\UserEventSubscriber as CoreUserEventSubscriber;
use Netgen\IbexaSearchExtra\Core\Search\Common\EventSubscriber\ContentEventSubscriber;
use Netgen\IbexaSearchExtra\Core\Search\Common\EventSubscriber\LocationEventSubscriber;
use Netgen\IbexaSearchExtra\Core\Search\Common\EventSubscriber\ObjectStateEventSubscriber;
use Netgen\IbexaSearchExtra\Core\Search\Common\EventSubscriber\SectionEventSubscriber;
use Netgen\IbexaSearchExtra\Core\Search\Common\EventSubscriber\TrashEventSubscriber;
use Netgen\IbexaSearchExtra\Core\Search\Common\EventSubscriber\UserEventSubscriber;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class AsynchronousIndexingPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $useAsynchronousIndexing = $container->getParameter(
            'netgen_ibexa_search_extra.use_asynchronous_indexing',
        );

        if ($useAsynchronousIndexing !== true) {
            return;
        }

        $container
            ->register(ContentEventSubscriber::class, ContentEventSubscriber::class)
            ->setDecoratedService(CoreContentEventSubscriber::class)
            ->setArguments([
                new Reference('netgen.ibexa_search_extra.asynchronous_indexing.messenger.bus'),
            ]);

        $container
            ->register(LocationEventSubscriber::class, LocationEventSubscriber::class)
            ->setDecoratedService(CoreLocationEventSubscriber::class)
            ->setArguments([
                new Reference('netgen.ibexa_search_extra.asynchronous_indexing.messenger.bus'),
            ]);

        $container
            ->register(ObjectStateEventSubscriber::class, ObjectStateEventSubscriber::class)
            ->setDecoratedService(CoreObjectStateEventSubscriber::class)
            ->setArguments([
                new Reference('netgen.ibexa_search_extra.asynchronous_indexing.messenger.bus'),
            ]);

        $container
            ->register(SectionEventSubscriber::class, SectionEventSubscriber::class)
            ->setDecoratedService(CoreSectionEventSubscriber::class)
            ->setArguments([
                new Reference('netgen.ibexa_search_extra.asynchronous_indexing.messenger.bus'),
            ]);

        $container
            ->register(TrashEventSubscriber::class, TrashEventSubscriber::class)
            ->setDecoratedService(CoreTrashEventSubscriber::class)
            ->setArguments([
                new Reference('netgen.ibexa_search_extra.asynchronous_indexing.messenger.bus'),
            ]);

        $container
            ->register(UserEventSubscriber::class, UserEventSubscriber::class)
            ->setDecoratedService(CoreUserEventSubscriber::class)
            ->setArguments([
                new Reference('netgen.ibexa_search_extra.asynchronous_indexing.messenger.bus'),
            ]);
    }
}
