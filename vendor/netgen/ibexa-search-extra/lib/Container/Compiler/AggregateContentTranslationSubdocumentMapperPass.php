<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Container\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use function array_keys;

/**
 * This compiler pass will register Content translation subdocument mappers.
 *
 * @see \Netgen\IbexaSearchExtra\Core\Search\Solr\SubdocumentMapper\ContentTranslationSubdocumentMapper
 * @see \Netgen\IbexaSearchExtra\Core\Search\Solr\SubdocumentMapper\ContentTranslationSubdocumentMapper\Aggregate
 */
final class AggregateContentTranslationSubdocumentMapperPass implements CompilerPassInterface
{
    private static string $aggregateMapperId = 'netgen.ibexa_search_extra.solr.subdocument_mapper.content_translation.aggregate';
    private static string $mapperTag = 'netgen.ibexa_search_extra.solr.subdocument_mapper.content_translation';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(self::$aggregateMapperId)) {
            return;
        }

        $aggregateDefinition = $container->getDefinition(self::$aggregateMapperId);
        $mapperIds = $container->findTaggedServiceIds(self::$mapperTag);

        $this->registerMappers($aggregateDefinition, $mapperIds);
    }

    private function registerMappers(Definition $definition, array $mapperIds): void
    {
        foreach (array_keys($mapperIds) as $id) {
            $definition->addMethodCall('addMapper', [new Reference($id)]);
        }
    }
}
