<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Container\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use function array_keys;

/**
 * This compiler pass will register subdocument Criterion visitors.
 *
 * @see \Ibexa\Solr\Query\Common\CriterionVisitor\Aggregate
 */
final class AggregateSubdocumentQueryCriterionVisitorPass implements CompilerPassInterface
{
    private const AggregateVisitorId = 'netgen.ibexa_search_extra.solr.query.content.criterion_visitor.subdocument_query.aggregate';
    private const VisitorTag = 'netgen.ibexa_search_extra.solr.query.content.criterion_visitor.subdocument_query';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(self::AggregateVisitorId)) {
            return;
        }

        $aggregateDefinition = $container->getDefinition(self::AggregateVisitorId);
        $mapperIds = $container->findTaggedServiceIds(self::VisitorTag);

        $this->registerMappers($aggregateDefinition, $mapperIds);
    }

    private function registerMappers(Definition $definition, array $visitorIds): void
    {
        foreach (array_keys($visitorIds) as $id) {
            $definition->addMethodCall('addVisitor', [new Reference($id)]);
        }
    }
}
