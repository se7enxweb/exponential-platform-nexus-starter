<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaScheduledVisibilityBundle\Core;

use function in_array;

final class Configuration
{
    public function __construct(
        private readonly string $handler,
        private readonly bool $enabled,
        private readonly bool $allContentTypes,
        private readonly array $allowedContentTypes,
    ) {}

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function isAllContentTypes(): bool
    {
        return $this->allContentTypes;
    }

    public function getAllowedContentTypes(): array
    {
        return $this->allowedContentTypes;
    }

    public function getHandlerIdentifier(): string
    {
        return $this->handler;
    }

    public function isContentTypeAllowed(string $contentType): bool
    {
        return $this->allContentTypes || in_array($contentType, $this->allowedContentTypes, true);
    }
}
