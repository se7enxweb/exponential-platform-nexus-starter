<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Tests\Integration\SetupFactory;

use Ibexa\Tests\Solr\SetupFactory\LegacySetupFactory as CoreSolrSetupFactory;
use Netgen\IbexaSearchExtra\Container\Compiler;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class RegressionSolr extends CoreSolrSetupFactory
{
    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    protected function externalBuildContainer(ContainerBuilder $containerBuilder): void
    {
        parent::externalBuildContainer($containerBuilder);

        $configPath = __DIR__ . '/../../../../lib/Resources/config/';
        $loader = new YamlFileLoader($containerBuilder, new FileLocator($configPath));
        $loader->load('search/common.yaml');
        $loader->load('search/solr_services.yaml');
        $loader->load('search/solr_engine.yaml');

        $testConfigPath = __DIR__ . '/../Resources/config/';
        $loader = new YamlFileLoader($containerBuilder, new FileLocator($testConfigPath));
        $loader->load('services.yaml');

        // Needs to be added first because other passes depend on it
        $containerBuilder->addCompilerPass(new Compiler\TagSubdocumentCriterionVisitorsPass());
        $containerBuilder->addCompilerPass(new Compiler\AggregateContentSubdocumentMapperPass());
        $containerBuilder->addCompilerPass(new Compiler\AggregateContentTranslationSubdocumentMapperPass());
        $containerBuilder->addCompilerPass(new Compiler\AggregateSubdocumentQueryCriterionVisitorPass());
        $containerBuilder->addCompilerPass(new Compiler\ElasticsearchExtensibleDocumentFactoryPass());
    }
}
