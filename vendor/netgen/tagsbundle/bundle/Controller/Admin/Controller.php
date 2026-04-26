<?php

declare(strict_types=1);

namespace Netgen\TagsBundle\Controller\Admin;

use Ibexa\Bundle\Core\Controller as BaseController;
use Ibexa\Contracts\User\Controller\RestrictedControllerInterface;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;
use Netgen\TagsBundle\Core\Pagination\Pagerfanta\TagAdapterInterface;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;
use Pagerfanta\PagerfantaInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

use function array_merge;

abstract class Controller extends BaseController implements RestrictedControllerInterface
{
    /**
     * Ensures that only authenticated users can access to controller.
     * It is not needed to call this method from actions
     * as it's already called from base controller service.
     *
     * @see netgen_tags.admin.controller.base service definition
     */
    public function performAccessCheck(): void
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                'translator' => TranslatorInterface::class,
            ],
        );
    }

    /**
     * Redirects to tag page or dashboard if tag is not provided.
     */
    protected function redirectToTag(?Tag $tag = null): RedirectResponse
    {
        if (!$tag instanceof Tag) {
            return $this->redirectToRoute('netgen_tags_admin_root');
        }

        return $this->redirectToRoute(
            'netgen_tags_admin_tag_show',
            [
                'tagId' => $tag->id,
            ],
        );
    }

    /**
     * Adds a flash message with specified parameters.
     */
    protected function addFlashMessage(string $messageType, string $message, array $parameters = []): void
    {
        /** @var \Symfony\Contracts\Translation\TranslatorInterface $translator */
        $translator = $this->container->get('translator');

        $this->addFlash(
            'tags.' . $messageType,
            $translator->trans(
                $messageType . '.' . $message,
                $parameters,
                'netgen_tags_admin_flash',
            ),
        );
    }

    /**
     * Creates a pager for use with various pages.
     *
     * @param \Pagerfanta\Adapter\AdapterInterface<\Ibexa\Contracts\Core\Repository\Values\ValueObject> $adapter
     *
     * @return \Pagerfanta\PagerfantaInterface<\Ibexa\Contracts\Core\Repository\Values\ValueObject>
     */
    protected function createPager(AdapterInterface $adapter, int $currentPage, int $maxPerPage, ?Tag $tag = null): PagerfantaInterface
    {
        if ($adapter instanceof TagAdapterInterface && $tag instanceof Tag) {
            $adapter->setTag($tag);
        }

        $pager = new Pagerfanta($adapter);

        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage($maxPerPage);
        $pager->setCurrentPage($currentPage > 0 ? $currentPage : 1);

        return $pager;
    }
}
