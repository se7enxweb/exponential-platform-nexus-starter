<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Tests\Integration\Implementation\Solr\SubdocumentMapper;

use Ibexa\Contracts\Core\Persistence\Content;
use Ibexa\Contracts\Core\Search\Document;
use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Contracts\Core\Search\FieldType;
use Netgen\IbexaSearchExtra\Core\Search\Solr\SubdocumentMapper\ContentTranslationSubdocumentMapper;
use function array_key_exists;
use function uniqid;

class TestContentTranslationSubdocumentMapper extends ContentTranslationSubdocumentMapper
{
    private static array $dataMap = [
        // Users
        '4' => [
            0 => [
                'visible' => true,
                'price' => 60,
            ],
            1 => [
                'visible' => false,
                'price' => 50,
            ],
        ],
        // Partners
        '59' => [
            0 => [
                'visible' => true,
                'price' => 40,
            ],
            1 => [
                'visible' => false,
                'price' => 50,
            ],
        ],
    ];

    public function accept(Content $content, $languageCode): bool
    {
        return $languageCode === 'ger-DE' && array_key_exists($content->versionInfo->contentInfo->id, static::$dataMap);
    }

    public function mapDocuments(Content $content, $languageCode): array
    {
        return [
            new Document([
                'id' => uniqid('test_content_translation_subdocument_', false),
                'fields' => [
                    new Field(
                        'document_type',
                        'test_content_translation_subdocument',
                        new FieldType\IdentifierField(),
                    ),
                    new Field(
                        'visible',
                        static::$dataMap[$content->versionInfo->contentInfo->id][0]['visible'],
                        new FieldType\BooleanField(),
                    ),
                    new Field(
                        'price',
                        static::$dataMap[$content->versionInfo->contentInfo->id][0]['price'],
                        new FieldType\IntegerField(),
                    ),
                ],
            ]),
            new Document([
                'id' => uniqid('test_content_translation_subdocument_', false),
                'fields' => [
                    new Field(
                        'document_type',
                        'test_content_translation_subdocument',
                        new FieldType\IdentifierField(),
                    ),
                    new Field(
                        'visible',
                        static::$dataMap[$content->versionInfo->contentInfo->id][1]['visible'],
                        new FieldType\BooleanField(),
                    ),
                    new Field(
                        'price',
                        static::$dataMap[$content->versionInfo->contentInfo->id][1]['price'],
                        new FieldType\IntegerField(),
                    ),
                ],
            ]),
        ];
    }
}
