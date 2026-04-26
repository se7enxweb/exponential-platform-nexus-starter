<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaAdminUIExtraBundle\Controller\Content;

use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Resolver\IconPathResolverInterface;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\BadStateException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

use function implode;

class VisibilityInfo extends Controller
{
    public function __construct(
        private readonly LocationService $locationService,
        private readonly TranslatorInterface $translator,
        private readonly ContentService $contentService,
        private readonly IconPathResolverInterface $iconPathResolver,
    ) {}

    public function __invoke(int $contentId, Request $request): Response
    {
        $iconPath = $this->iconPathResolver->resolve('hide');

        $content = $this->contentService->loadContent($contentId);

        $extraLines = [];
        if ($content->getContentInfo()->isHidden() === true) {
            $extraLines[] = $this->translator->trans('content.visibility.content_hidden', [], 'locationview');
        }

        try {
            $locations = $this->locationService->loadLocations($content->getContentInfo());

            $explicitlyHiddenLocations = 0;
            $hiddenByAncestorLocations = 0;
            foreach ($locations as $location) {
                if ($location->explicitlyHidden) {
                    $explicitlyHiddenLocations++;
                }

                if ($location->isInvisible() && $location->getParentLocation()?->isInvisible()) {
                    $hiddenByAncestorLocations++;
                }
            }

            $explicitlyHiddenLocationsMessage = null;
            $hiddenByAncestorLocationsMessage = null;
            if (count($locations) === 1) {
                if ($explicitlyHiddenLocations !== 0) {
                    $explicitlyHiddenLocationsMessage = $this->translator->trans('content.visibility.location_hidden', [], 'locationview');
                }

                if ($hiddenByAncestorLocations !== 0) {
                    $hiddenByAncestorLocationsMessage = $this->translator->trans('content.visibility.location_hidden_by_ancestor', [], 'locationview');
                }
            } else {
                if ($explicitlyHiddenLocations !== 0) {
                    $explicitlyHiddenLocationsMessage = $this->translator->trans(
                        'content.visibility.locations_hidden',
                        [
                            '%hidden%' => $explicitlyHiddenLocations,
                            '%total%' => count($locations),
                        ],
                        'locationview',
                    );
                }

                if ($hiddenByAncestorLocations !== 0) {
                    $hiddenByAncestorLocationsMessage = $this->translator->trans(
                        'content.visibility.locations_hidden_by_ancestor',
                        [
                            '%hidden%' => $hiddenByAncestorLocations,
                            '%total%' => count($locations),
                        ],
                        'locationview',
                    );
                }
            }

            $extraLines[] = $explicitlyHiddenLocationsMessage;
            $extraLines[] = $hiddenByAncestorLocationsMessage;
            $extraLines = array_filter($extraLines);
        } catch (BadStateException $e) {
            $extraLines[] = $this->translator->trans('content.visibility.cannot_fetch_locations', [], 'locationview');
        }

        if ($extraLines === []) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        return $this->render('@IbexaAdminUi/themes/admin/ui/component/alert/alert.html.twig',
            [
                'type' => 'info',
                'title' => $this->translator->trans('content.visibility.info', [], 'locationview'),
                'extra_content' => implode('<br>', $extraLines),
                'icon_path' => $iconPath,
                'class' => 'mt-4',
            ],
        );
    }
}
