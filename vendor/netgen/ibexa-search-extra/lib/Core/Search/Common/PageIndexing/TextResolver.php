<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\PageIndexing;

use Ibexa\Contracts\Core\Persistence\Content\ContentInfo;
use Netgen\IbexaSearchExtra\Core\Search\Common\PageIndexing\Exception\PageUnavailableException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class TextResolver
{
    /** @var array<int, array<string, array<string, array<int, string>|string>>> */
    private array $cache = [];

    public function __construct(
        private readonly UrlResolver $urlResolver,
        private readonly SourceFetcher $sourceFetcher,
        private readonly TextExtractor $textExtractor,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {}

    /**
     * @return array<string, array<int, string>>
     */
    public function resolveText(ContentInfo $contentInfo, string $languageCode): array
    {
        $contentId = $contentInfo->id;

        if (isset($this->cache[$contentId][$languageCode])) {
            return $this->cache[$contentId][$languageCode];
        }

        try {
            $url = $this->urlResolver->resolveUrl($contentInfo, $languageCode);
            $source = $this->sourceFetcher->fetchSource($url);
        } catch (PageUnavailableException $e) {
            $this->logger->error($e->getMessage());

            return [];
        }

        $textArray = $this->textExtractor->extractText($source, $contentInfo, $languageCode);

        $this->cache[$contentId][$languageCode] = $textArray;

        return $textArray;
    }
}
