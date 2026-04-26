<?php

declare(strict_types=1);

namespace Netgen\IbexaFieldTypeEnhancedLink\FieldType;

use Ibexa\Contracts\Core\Persistence\Content;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Core\FieldType\ValidationError;

use function in_array;

/**
 * Validator for checking existence of content and its content type.
 */
class InternalLinkValidator
{
    public function __construct(
        private Content\Handler $contentHandler,
        private Content\Type\Handler $contentTypeHandler,
    ) {}

    public function validate(Value $value, array $allowedContentTypes = []): ?ValidationError
    {
        try {
            if ($value->isTypeInternal()) {
                $content = $this->contentHandler->load((int) $value->reference);
                $contentType = $this->contentTypeHandler->load($content->versionInfo->contentInfo->contentTypeId);
                if (!empty($allowedContentTypes) && !in_array($contentType->identifier, $allowedContentTypes, true)) {
                    return new ValidationError(
                        'Content Type %contentTypeIdentifier% is not a valid relation target',
                        null,
                        [
                            '%contentTypeIdentifier%' => $contentType->identifier,
                        ],
                        'targetContentId',
                    );
                }
            }
        } catch (NotFoundException $e) {
            return new ValidationError(
                'Content with identifier %contentId% is not a valid enhanced link target',
                null,
                [
                    '%contentId%' => (int) $value->reference,
                ],
                'targetContentId',
            );
        }

        return null;
    }
}
