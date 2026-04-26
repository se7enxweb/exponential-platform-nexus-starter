<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Container\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Ibexa\Solr\Query\Common\CriterionVisitor\LogicalAnd;
use Ibexa\Solr\Query\Common\CriterionVisitor\LogicalNot;
use Ibexa\Solr\Query\Common\CriterionVisitor\LogicalOr;
use Ibexa\Solr\Query\Common\CriterionVisitor\CustomField\CustomFieldIn;
use Ibexa\Solr\Query\Common\CriterionVisitor\CustomField\CustomFieldRange;

/**
 * This compiler pass will add the 'netgen.ibexa_search_extra.solr.criterion_visitor.subdocument_query' tag to the
 * selected Ibexa CMS provided criterion visitors.
 */
final class TagSubdocumentCriterionVisitorsPass implements CompilerPassInterface
{
    private static string $subdocumentCriterionVisitorTag = 'netgen.ibexa_search_extra.solr.query.content.criterion_visitor.subdocument_query';
    private static array $criterionVisitorIds = [
        LogicalAnd::class,
        LogicalNot::class,
        LogicalOr::class,
        CustomFieldIn::class,
        CustomFieldRange::class,
    ];

    public function process(ContainerBuilder $container): void
    {
        foreach (self::$criterionVisitorIds as $id) {
            if (!$container->hasDefinition($id)) {
                continue;
            }

            $definition = $container->getDefinition($id);
            $definition->addTag(self::$subdocumentCriterionVisitorTag);
        }
    }
}
