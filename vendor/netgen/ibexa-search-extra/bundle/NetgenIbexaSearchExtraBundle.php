<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaSearchExtraBundle;

use Netgen\Bundle\IbexaSearchExtraBundle\DependencyInjection\Compiler\UrlResolverOverridePass;
use Netgen\IbexaSearchExtra\Container\Compiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NetgenIbexaSearchExtraBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        // Needs to be added first because other passes depend on it
        $container->addCompilerPass(new Compiler\TagSubdocumentCriterionVisitorsPass());
        $container->addCompilerPass(new Compiler\AggregateContentSubdocumentMapperPass());
        $container->addCompilerPass(new Compiler\AggregateContentTranslationSubdocumentMapperPass());
        $container->addCompilerPass(new Compiler\AggregateSubdocumentQueryCriterionVisitorPass());
        $container->addCompilerPass(new Compiler\AsynchronousIndexingPass());
        $container->addCompilerPass(new Compiler\FieldType\RichTextIndexablePass());
        $container->addCompilerPass(new Compiler\SearchResultExtractorPass());
        $container->addCompilerPass(new Compiler\ElasticsearchExtensibleDocumentFactoryPass());
        $container->addCompilerPass(new UrlResolverOverridePass());
    }
}
