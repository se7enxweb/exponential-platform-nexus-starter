<?php

declare(strict_types=1);

namespace Netgen\Bundle\ToolbarBundle\SiteAccess;

use Ibexa\Core\MVC\Symfony\SiteAccess;

use function array_key_exists;

final class AdminSiteAccessResolver
{
    /**
     * @param array<string, string> $siteAccessMapping
     * @param array<string, string[]> $groupsBySiteAccess
     */
    public function __construct(
        private array $siteAccessMapping,
        private string $defaultAdminSiteAccess,
        private array $groupsBySiteAccess,
        private SiteAccess $siteAccess,
    ) {}

    public function resolveAdminSiteAccess(): string
    {
        if (array_key_exists($this->siteAccess->name, $this->siteAccessMapping)) {
            return $this->siteAccessMapping[$this->siteAccess->name];
        }

        foreach ($this->groupsBySiteAccess[$this->siteAccess->name] ?? [] as $group) {
            if (array_key_exists($group, $this->siteAccessMapping)) {
                return $this->siteAccessMapping[$group];
            }
        }

        return $this->defaultAdminSiteAccess;
    }
}
