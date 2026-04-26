<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaSearchExtraBundle\Core\Search\Common\PageIndexing\UrlResolver;

use Ibexa\Contracts\Core\Persistence\Content\ContentInfo;
use Netgen\IbexaSearchExtra\Core\Search\Common\PageIndexing\ConfigResolver;
use Netgen\IbexaSearchExtra\Core\Search\Common\PageIndexing\UrlResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class SiteUrlResolver extends UrlResolver
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly ConfigResolver $configResolver,
    ) {}

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function resolveUrl(ContentInfo $contentInfo, string $languageCode): string
    {
        $config = $this->configResolver->resolveConfig($contentInfo, $languageCode);
        /** @var \Ibexa\Bundle\Core\Routing\UrlAliasRouter $router */
        $router = $this->router;

        $urlAliasRouteName = 'ibexa.url.alias';

        if ($config->hasHost()) {
            $relativePath = $router->generate(
                $urlAliasRouteName,
                [
                    'locationId' => (int) $contentInfo->mainLocationId,
                    'siteaccess' => $config->getSiteaccess(),
                ],
                UrlGeneratorInterface::RELATIVE_PATH,
            );

            return $config->getHost() . $relativePath;
        }

        return $router->generate(
            $urlAliasRouteName,
            [
                'locationId' => (int) $contentInfo->mainLocationId,
                'siteaccess' => $config->getSiteaccess(),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );
    }
}
