<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\FieldType\RichText;

use DOMDocument;
use DOMNode;
use Ibexa\Contracts\Core\FieldType\Indexable as IndexableInterface;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Contracts\Core\Search;
use function mb_substr;
use function strtok;
use function trim;

/**
 * Indexable definition for RichText field type.
 */
final class Indexable implements IndexableInterface
{
    /**
     * @var int
     */
    private $shortTextMaxLength;

    public function __construct($shortTextMaxLength = 256)
    {
        $this->shortTextMaxLength = $shortTextMaxLength;
    }

    public function getIndexData(Field $field, FieldDefinition $fieldDefinition): array
    {
        $document = new DOMDocument();
        $document->loadXML($field->value->data);
        $text = $this->extractText($document->documentElement);
        $shortText = $this->shortenText($text);

        return [
            new Search\Field(
                'fulltext',
                $text,
                new Search\FieldType\FullTextField(),
            ),
            new Search\Field(
                'value',
                $shortText,
                new Search\FieldType\StringField(),
            ),
        ];
    }

    public function getIndexDefinition(): array
    {
        return [
            'value' => new Search\FieldType\StringField(),
        ];
    }

    public function getDefaultMatchField(): string
    {
        return 'value';
    }

    public function getDefaultSortField(): string
    {
        return $this->getDefaultMatchField();
    }

    /**
     * Extracts text content of the given $node.
     *
     * @param \DOMNode $node
     *
     * @return string
     */
    private function extractText(DOMNode $node): string
    {
        $text = '';

        if ($node->childNodes !== null && $node->childNodes->length > 0) {
            foreach ($node->childNodes as $child) {
                $text .= $this->extractText($child);
            }
        } else {
            $text .= $node->nodeValue . ' ';
        }

        return $text;
    }

    /**
     * Shorten text from the given $text.
     */
    private function shortenText(string $text): string
    {
        return mb_substr(trim(strtok($text, "\r\n")), 0, $this->shortTextMaxLength);
    }
}
