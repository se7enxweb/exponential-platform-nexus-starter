<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Container\Compiler;

use Netgen\IbexaSearchExtra\Core\Search\Solr\ResultExtractor\NativeResultExtractor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Configures a native search result extractor if the loading search result extractor is disabled.
 *
 * @see \Netgen\IbexaSearchExtra\Core\Search\Solr\ResultExtractor\NativeResultExtractor
 */
final class SearchResultExtractorPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function process(ContainerBuilder $container): void
    {
        $useLoadingSearchResultExtractor = $container->getParameter(
            'netgen_ibexa_search_extra.use_loading_search_result_extractor',
        );

        if ($useLoadingSearchResultExtractor === true) {
            return;
        }

        $serviceId = 'netgen.ibexa_search_extra.solr.result_extractor.content.native_override';
        $decoratedServiceId = 'ibexa.solr.result_extractor.content.native';

        $container
            ->register($serviceId, NativeResultExtractor::class)
            ->setDecoratedService($decoratedServiceId)
            ->setArguments([
                new Reference($serviceId . '.inner'),
                new Reference('ibexa.solr.query.content.aggregation_result_extractor.dispatcher'),
                new Reference('Ibexa\Solr\Gateway\EndpointRegistry'),
            ]);

        $serviceId = 'netgen.ibexa_search_extra.solr.result_extractor.location.native_override';
        $decoratedServiceId = 'ibexa.solr.result_extractor.location.native';

        $container
            ->register($serviceId, NativeResultExtractor::class)
            ->setDecoratedService($decoratedServiceId)
            ->setArguments([
                new Reference($serviceId . '.inner'),
                new Reference('ibexa.solr.query.location.aggregation_result_extractor.dispatcher'),
                new Reference('Ibexa\Solr\Gateway\EndpointRegistry'),
            ]);
    }
}
