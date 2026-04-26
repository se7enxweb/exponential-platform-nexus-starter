<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaSearchExtraBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class UrlResolverOverridePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('netgen.ibexa_search_extra.page_indexing.url_resolver.site')) {
            return;
        }

        $container->setAlias(
            'netgen.ibexa_search_extra.page_indexing.url_resolver',
            new Alias('netgen.ibexa_search_extra.page_indexing.url_resolver.site', false),
        );
    }
}
