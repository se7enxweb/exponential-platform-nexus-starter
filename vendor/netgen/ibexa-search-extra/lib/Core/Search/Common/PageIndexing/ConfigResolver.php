<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\PageIndexing;

use Ibexa\Contracts\Core\Persistence\Content\ContentInfo;
use Ibexa\Contracts\Core\Persistence\Content\Location\Handler as LocationHandler;
use Netgen\IbexaSearchExtra\Core\Search\Common\PageIndexing\Exception\MissingConfigException;

use function explode;
use function in_array;

class ConfigResolver
{
    /**
     * @param array<string, mixed> $configuration
     */
    public function __construct(
        private readonly LocationHandler $locationHandler,
        private readonly array $configuration,
    ) {}

    /**
     * @throws \Netgen\IbexaSearchExtra\Core\Search\Common\PageIndexing\Exception\MissingConfigException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function resolveConfig(ContentInfo $contentInfo, string $languageCode): Config
    {
        $location = $this->locationHandler->load($contentInfo->mainLocationId);

        $pathString = $location->pathString;
        $pathArray = array_map('intval', explode('/', $pathString));

        foreach ($this->configuration as $siteConfiguration) {
            if (!in_array($siteConfiguration['tree_root_location_id'], $pathArray, true)) {
                continue;
            }

            $languageSiteaccessMap = $siteConfiguration['language_siteaccess_map'] ?? [];
            $siteaccess = $this->resolveSiteaccessForLanguage($languageCode, $languageSiteaccessMap);

            if ($siteaccess === null) {
                continue;
            }

            return $this->mapConfig($siteaccess, $siteConfiguration);
        }

        throw new MissingConfigException($contentInfo, $languageCode);
    }

    private function resolveSiteaccessForLanguage(string $languageCode, array $languageSiteaccessMap): ?string
    {
        foreach ($languageSiteaccessMap as $mappedLanguageCode => $siteaccess) {
            if ($languageCode === $mappedLanguageCode) {
                return $siteaccess;
            }
        }

        return null;
    }

    private function mapConfig(string $siteaccess, array $siteConfiguration): Config
    {
        return new Config(
            $siteaccess,
            $siteConfiguration['allowed_content_types'],
            $siteConfiguration['fields'],
            $siteConfiguration['host'],
        );
    }
}
