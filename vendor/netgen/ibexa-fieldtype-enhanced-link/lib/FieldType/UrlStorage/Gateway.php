<?php

declare(strict_types=1);

namespace Netgen\IbexaFieldTypeEnhancedLink\FieldType\UrlStorage;

use Ibexa\Contracts\Core\FieldType\StorageGateway;

/**
 * Abstract gateway class for enhanced link field type.
 * Handles external link URL data.
 */
abstract class Gateway extends StorageGateway
{
    /**
     * Returns a list of URLs for a list of URL ids.
     *
     * Non-existent ids are ignored.
     *
     * @param int[]|string[] $ids An array of URL ids
     *
     * @return array An array of URLs, with ids as keys
     */
    abstract public function getIdUrlMap(array $ids): array;

    /**
     * Returns a list of URL ids for a list of URLs.
     *
     * Non-existent URLs are ignored.
     *
     * @param string[] $urls An array of URLs
     *
     * @return array<string,int> An array of URL ids, with URLs as keys
     */
    abstract public function getUrlIdMap(array $urls): array;

    /**
     * Inserts a new $url and returns its id.
     *
     * @param string $url The URL to insert in the database
     *
     * @return int
     */
    abstract public function insertUrl(string $url): int;

    /**
     * Creates link to URL with $urlId for field with $fieldId in $versionNo.
     */
    abstract public function linkUrl(int $urlId, int $fieldId, int $versionNo): void;

    /**
     * Removes link to URL for $fieldId in $versionNo and cleans up possibly orphaned URLs.
     *
     * @param int[] $excludeUrlIds
     */
    abstract public function unlinkUrl(int $fieldId, int $versionNo, array $excludeUrlIds = []);
}
