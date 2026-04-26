<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Common\PageIndexing\TextExtractor;

use DOMDocument;
use DOMNode;
use Ibexa\Contracts\Core\Persistence\Content\ContentInfo;
use Netgen\IbexaSearchExtra\Core\Search\Common\PageIndexing\Config;
use Netgen\IbexaSearchExtra\Core\Search\Common\PageIndexing\ConfigResolver;
use Netgen\IbexaSearchExtra\Core\Search\Common\PageIndexing\TextExtractor;

use function explode;
use function in_array;
use function libxml_use_internal_errors;
use function mb_strlen;
use function mb_strpos;
use function mb_substr;
use function trim;

use const XML_ELEMENT_NODE;
use const XML_HTML_DOCUMENT_NODE;
use const XML_TEXT_NODE;

class NativeTextExtractor extends TextExtractor
{
    public function __construct(
        private readonly ConfigResolver $configResolver,
    ) {}

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function extractText(string $source, ContentInfo $contentInfo, string $languageCode): array
    {
        $startTag = '<!--begin page content-->';
        $endTag = '<!--end page content-->';
        $config = $this->configResolver->resolveConfig($contentInfo, $languageCode);

        $startPos = mb_strpos($source, $startTag);
        $endPos = mb_strpos($source, $endTag);

        $textArray = [];

        if ($startPos !== false && $endPos !== false) {
            $startPos += mb_strlen($startTag);
            $extractedContent = mb_substr($source, $startPos, $endPos - $startPos);

            libxml_use_internal_errors(true);
            $doc = new DOMDocument();
            $doc->loadHTML($extractedContent);
            libxml_use_internal_errors(false);

            $textArray = $this->recursiveExtractTextArray($doc, $textArray, $config);
        }

        return $textArray;
    }

    /**
     * @param array<string, array<int, string>> $textArray
     *
     * @return array<string, array<int, string>>
     */
    private function recursiveExtractTextArray(DOMNode $node, array &$textArray, Config $config): array
    {
        if ($node->nodeType === XML_ELEMENT_NODE || $node->nodeType === XML_HTML_DOCUMENT_NODE) {
            $fieldName = $this->resolveFieldName($node, $config);

            if ($fieldName !== null) {
                $textArray[$fieldName][] = $node->textContent;

                return $textArray;
            }

            foreach ($node->childNodes as $childNode) {
                $this->recursiveExtractTextArray($childNode, $textArray, $config);
            }
        }

        if ($node->nodeType === XML_TEXT_NODE) {
            $textContent = trim($node->textContent);

            if ($textContent !== '') {
                $textArray['other'][] = $textContent;
            }
        }

        return $textArray;
    }

    private function resolveFieldName(DOMNode $node, Config $config): ?string
    {
        foreach ($config->getFields() as $level => $tags) {
            foreach ($tags as $tag) {
                $tagParts = explode('.', $tag); // Split tag and class if present
                $tagName = $tagParts[0]; // Get the tag name
                $class = $tagParts[1] ?? null; // Get the class if exists

                if ($node->nodeName !== $tagName) {
                    continue;
                }

                if ($class !== null && !$this->hasClass($node, $class)) {
                    continue;
                }

                return $level;
            }
        }

        return null;
    }

    private function hasClass(DOMNode $node, string $className): bool
    {
        /** @var \DOMElement $node */
        $classes = explode(' ', $node->getAttribute('class'));

        return in_array($className, $classes, true);
    }
}
