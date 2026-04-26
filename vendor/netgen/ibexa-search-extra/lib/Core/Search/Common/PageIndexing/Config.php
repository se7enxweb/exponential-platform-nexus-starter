<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\PageIndexing;

final class Config
{
    /**
     * @param string[] $allowedContentTypes
     * @param array<string, array<int, string>> $fields
     */
    public function __construct(
        private readonly string $siteaccess,
        private readonly array $allowedContentTypes,
        private readonly array $fields,
        private readonly ?string $host,
    ) {
    }

    public function getSiteaccess(): string
    {
        return $this->siteaccess;
    }

    public function getAllowedContentTypes(): array
    {
        return $this->allowedContentTypes;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function hasHost(): bool
    {
        return $this->host !== null;
    }
}
