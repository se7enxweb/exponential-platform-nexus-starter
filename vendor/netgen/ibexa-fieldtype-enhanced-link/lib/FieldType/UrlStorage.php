<?php

declare(strict_types=1);

namespace Netgen\IbexaFieldTypeEnhancedLink\FieldType;

use Ibexa\Contracts\Core\FieldType\GatewayBasedStorage;
use Ibexa\Contracts\Core\FieldType\StorageGatewayInterface;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class UrlStorage extends GatewayBasedStorage
{
    /** @param \Netgen\IbexaFieldTypeEnhancedLink\FieldType\UrlStorage\Gateway $gateway */
    public function __construct(
        protected StorageGatewayInterface $gateway,
        protected ?LoggerInterface $logger = null,
    ) {
        parent::__construct($gateway);
        $this->logger ??= new NullLogger();
    }

    public function storeFieldData(VersionInfo $versionInfo, Field $field): bool
    {
        $url = (string) $field->value->externalData;

        if (empty($url)) {
            return false;
        }

        $map = $this->gateway->getUrlIdMap([$url]);
        $urlId = $map[$url] ?? $this->gateway->insertUrl($url);
        $this->gateway->linkUrl($urlId, $field->id, $versionInfo->versionNo);

        $this->gateway->unlinkUrl(
            $field->id,
            $versionInfo->versionNo,
            [$urlId],
        );

        $field->value->data['id'] = $urlId;

        return true;
    }

    public function getFieldData(VersionInfo $versionInfo, Field $field): void
    {
        if ($field->value->data === null) {
            return;
        }

        $id = $field->value->data['id'];
        $type = $field->value->data['type'] ?? null;

        if (empty($id) || $type !== Type::LINK_TYPE_EXTERNAL) {
            return;
        }

        $map = $this->gateway->getIdUrlMap([$id]);

        if (!isset($map[$id])) {
            $this->logger->error("URL with ID '{$id}' not found");
        }

        $field->value->externalData = $map[$id] ?? null;
    }

    public function deleteFieldData(VersionInfo $versionInfo, array $fieldIds): void
    {
        foreach ($fieldIds as $fieldId) {
            $this->gateway->unlinkUrl($fieldId, $versionInfo->versionNo);
        }
    }

    public function hasFieldData(): bool
    {
        return true;
    }

    public function getIndexData(VersionInfo $versionInfo, Field $field, array $context): void {}
}
