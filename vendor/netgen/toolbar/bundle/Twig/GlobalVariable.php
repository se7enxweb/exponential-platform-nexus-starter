<?php

declare(strict_types=1);

namespace Netgen\Bundle\ToolbarBundle\Twig;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ConfigResolver;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Netgen\Bundle\ToolbarBundle\SiteAccess\AdminSiteAccessResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use function array_key_exists;
use function mb_trim;
use function sprintf;

final class GlobalVariable
{
    /**
     * @param array<string, string> $activatedBundles
     */
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private ConfigResolverInterface $configResolver,
        private AdminSiteAccessResolver $adminSiteAccessResolver,
        private string $legacyAdminSiteAccessName,
        private array $activatedBundles,
    ) {}

    public function getAdminUrlTemplate(): string
    {
        $baseUrl = $this->urlGenerator->generate(
            'ibexa.url.alias',
            [
                'locationId' => $this->configResolver->getParameter('content.tree_root.location_id', null, ConfigResolver::SCOPE_DEFAULT),
                'siteaccess' => $this->getAdminSiteAccessName(),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        if ($this->hasLegacyAdmin()) {
            return sprintf('%s/%s', mb_trim($baseUrl, '/'), 'content/view/full/{locationId}');
        }

        return sprintf('%s/%s', mb_trim($baseUrl, '/'), 'view/content/{contentId}');
    }

    public function getAdminSiteAccessName(): string
    {
        $adminSiteAccessName = $this->adminSiteAccessResolver->resolveAdminSiteAccess();

        if ($this->hasLegacyAdmin()) {
            return $this->legacyAdminSiteAccessName;
        }

        return $adminSiteAccessName;
    }

    private function hasLegacyAdmin(): bool
    {
        return array_key_exists('NetgenAdminUIBundle', $this->activatedBundles);
    }
}
