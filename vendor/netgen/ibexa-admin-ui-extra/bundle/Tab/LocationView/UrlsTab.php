<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaAdminUIExtraBundle\Tab\LocationView;

use Ibexa\AdminUi\Tab\LocationView\UrlsTab as IbexaUrlsTab;
use Ibexa\Contracts\AdminUi\Tab\AbstractEventDispatchingTab;
use Ibexa\Contracts\AdminUi\Tab\OrderedTabInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

use function in_array;
use function preg_match;

final class UrlsTab extends AbstractEventDispatchingTab implements OrderedTabInterface
{
    private const SYSTEM_URL_REGEX_PATTERN = '#/view/content/\d+/full/\d+/\d+$#';

    public function __construct(
        private readonly IbexaUrlsTab $inner,
        private readonly RouterInterface $router,
        private readonly ConfigResolverInterface $configResolver,
        private readonly array $siteaccessList,
        private readonly bool $showSiteaccessUrlsOutsideConfiguredContentTreeRoot,
        Environment $twig,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
    ) {
        parent::__construct($twig, $translator, $eventDispatcher);
    }

    public function getIdentifier(): string
    {
        return $this->inner->getIdentifier();
    }

    public function getName(): string
    {
        return $this->inner->getName();
    }

    public function getOrder(): int
    {
        return $this->inner->getOrder();
    }

    public function getTemplate(): string
    {
        return $this->inner->getTemplate();
    }

    public function getTemplateParameters(array $contextParameters = []): array
    {
        $siteaccessUrls = [];
        $siteaccessUrlsOutsideConfiguredContentTreeRoot = [];

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location */
        $location = $contextParameters['location'];

        foreach ($this->siteaccessList as $siteaccess) {
            $url = $this->router->generate(
                'ibexa.url.alias',
                [
                    'locationId' => $location->id,
                    'siteaccess' => $siteaccess,
                ],
                UrlGeneratorInterface::ABSOLUTE_URL,
            );

            // checks if the url matches invalid system url format
            if (preg_match(self::SYSTEM_URL_REGEX_PATTERN, $url)) {
                continue;
            }

            if ($this->isUnderConfiguredContentTreeRoot($location, $siteaccess)) {
                $siteaccessUrls[$siteaccess] = $url;
            } else {
                $siteaccessUrlsOutsideConfiguredContentTreeRoot[$siteaccess] = $url;
            }
        }

        $parameters = [
            'siteaccess_urls' => $siteaccessUrls,
            'siteaccess_urls_outside_configured_content_tree_root' => $siteaccessUrlsOutsideConfiguredContentTreeRoot,
            'show_siteaccess_urls_outside_configured_content_tree_root' => $this->showSiteaccessUrlsOutsideConfiguredContentTreeRoot,
        ];

        $parentParameters = $this->inner->getTemplateParameters($contextParameters);

        return $parentParameters + $parameters;
    }

    private function isUnderConfiguredContentTreeRoot(Location $location, string $siteaccess): bool
    {
        $rootLocationId = $this->configResolver->getParameter(
            'content.tree_root.location_id',
            null,
            $siteaccess,
        );

        return in_array((string) $rootLocationId, $location->path, true);
    }
}
