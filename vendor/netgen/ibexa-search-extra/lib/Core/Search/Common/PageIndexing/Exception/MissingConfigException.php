<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\PageIndexing\Exception;

use Ibexa\Contracts\Core\Persistence\Content\ContentInfo;
use RuntimeException;

class MissingConfigException extends RuntimeException
{
    public function __construct(ContentInfo $contentInfo, string $languageCode)
    {
        parent::__construct(
            sprintf(
                'Configuration for Content #%d in language "%s" is missing',
                $contentInfo->id,
                $languageCode,
            ),
        );
    }
}
