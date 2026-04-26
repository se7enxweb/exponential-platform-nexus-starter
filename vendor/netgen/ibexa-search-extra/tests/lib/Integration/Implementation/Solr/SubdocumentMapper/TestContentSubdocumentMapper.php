<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Tests\Integration\Implementation\Solr\SubdocumentMapper;

use Ibexa\Contracts\Core\Persistence\Content;
use Ibexa\Contracts\Core\Search\Document;
use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Contracts\Core\Search\FieldType;
use Netgen\IbexaSearchExtra\Core\Search\Solr\SubdocumentMapper\ContentSubdocumentMapper;
use function array_key_exists;
use function uniqid;

/**
 * Note: here we are only simulating indexing children data.
 */
class TestContentSubdocumentMapper extends ContentSubdocumentMapper
{
    private static array $dataMap = [
        // Administrator Users
        '12' => [
            0 => [
                'visible' => true,
                'price' => 40,
            ],
            1 => [
                'visible' => false,
                'price' => 50,
            ],
        ],
        // Anonymous Users
        '42' => [
            0 => [
                'visible' => true,
                'price' => 60,
            ],
            1 => [
                'visible' => false,
                'price' => 50,
            ],
        ],
    ];

    public function accept(Content $content): bool
    {
        return array_key_exists($content->versionInfo->contentInfo->id, static::$dataMap);
    }

    public function mapDocuments(Content $content): array
    {
        return [
            new Document([
                'id' => uniqid('test_content_subdocument_', false),
                'fields' => [
                    new Field(
                        'document_type',
                        'test_content_subdocument',
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
                'id' => uniqid('test_content_subdocument_', false),
                'fields' => [
                    new Field(
                        'document_type',
                        'test_content_subdocument',
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
