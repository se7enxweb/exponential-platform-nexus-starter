<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\PageIndexing\Exception;

use RuntimeException;

class PageUnavailableException extends RuntimeException
{
    public function __construct(string $url, string $message)
    {
        parent::__construct(
            sprintf(
                'Could not fetch page "%s": %s',
                $url,
                $message,
            ),
        );
    }
}
