<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\PageIndexing\UrlResolver;

use Ibexa\Contracts\Core\Persistence\Content\ContentInfo;
use Netgen\IbexaSearchExtra\Core\Search\Common\PageIndexing\Exception\PageUnavailableException;
use Netgen\IbexaSearchExtra\Core\Search\Common\PageIndexing\UrlResolver;

final class CoreUrlResolver extends UrlResolver
{
    public function resolveUrl(ContentInfo $contentInfo, string $languageCode): string
    {
        throw new PageUnavailableException(
            'n/a',
            'UrlResolver needs to be implemented in your MVC integration layer',
        );
    }
}
