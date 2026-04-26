<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaAdminUIExtraBundle\Controller\Content;

use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Symfony\Component\HttpFoundation\Response;

class LastModifiedInfo extends Controller
{
    public function __invoke(Content $content): Response
    {
        $lastContributor = $content->getVersionInfo()->getCreator();

        return $this->render(
            '@NetgenIbexaAdminUIExtra/themes/ngadmin/ui/last_modified_info.html.twig',
            [
                'last_contributor_name' => $lastContributor->getName(),
                'content_info' => $content->getContentInfo(),
            ],
        );
    }
}
