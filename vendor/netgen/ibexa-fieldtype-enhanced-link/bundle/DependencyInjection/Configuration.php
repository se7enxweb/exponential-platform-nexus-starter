<?php

declare(strict_types=1);

namespace Netgen\IbexaFieldTypeEnhancedLinkBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getAlias(): string
    {
        return 'netgen_ibexa_fieldtype_enhanced_link';
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        return new TreeBuilder('netgen_ibexa_fieldtype_enhanced_link');
    }
}
