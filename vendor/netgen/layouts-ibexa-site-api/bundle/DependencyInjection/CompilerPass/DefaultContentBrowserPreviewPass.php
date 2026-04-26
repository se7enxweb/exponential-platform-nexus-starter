<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsIbexaSiteApiBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use function is_array;
use function sprintf;

final class DefaultContentBrowserPreviewPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('ibexa.site_access.list')) {
            return;
        }

        $defaultRule = [
            'template' => $container->getParameter(
                'netgen_content_browser.ibexa.preview_template',
            ),
            'match' => [],
            'params' => [],
        ];

        /** @var string[] $siteAccessList */
        $siteAccessList = $container->getParameter('ibexa.site_access.list');
        $scopes = ['default', ...$siteAccessList];

        foreach ($scopes as $scope) {
            if ($container->hasParameter(sprintf('ibexa.site_access.config.%s.ng_content_view', $scope))) {
                /** @var array<string, mixed[]>|null $scopeRules */
                $scopeRules = $container->getParameter(sprintf('ibexa.site_access.config.%s.ng_content_view', $scope));
                $scopeRules = $this->addDefaultPreviewRule($scopeRules, $defaultRule);
                $container->setParameter(sprintf('ibexa.site_access.config.%s.ng_content_view', $scope), $scopeRules);
            }
        }
    }

    /**
     * Adds the default Site API content preview template to default scope as a fallback
     * when no preview rules are defined.
     *
     * @param array<string, mixed[]>|null $scopeRules
     * @param array<string, mixed> $defaultRule
     *
     * @return array<string, mixed[]>
     */
    private function addDefaultPreviewRule(?array $scopeRules, array $defaultRule): array
    {
        $scopeRules = is_array($scopeRules) ? $scopeRules : [];

        $contentBrowserRules = $scopeRules['ngcb_preview'] ?? [];

        $contentBrowserRules += [
            '___ngcb_preview_default___' => $defaultRule,
        ];

        $scopeRules['ngcb_preview'] = $contentBrowserRules;

        return $scopeRules;
    }
}
