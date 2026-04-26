<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaScheduledVisibilityBundle\Core;

use DateTime;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\FieldType\Date\Value as DateValue;
use Ibexa\Core\FieldType\DateAndTime\Value as DateAndTimeValue;
use Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\Enum\VisibilityUpdateResult;
use Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\Exception\InvalidStateException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

use function sprintf;

final class ScheduledVisibilityService
{
    public function __construct(
        private readonly Configuration $configurationService,
        private readonly Registry $registry,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {}

    public function updateVisibilityIfNeeded(Content $content, ?string $handlerIdentifier = null): VisibilityUpdateResult
    {
        $handler = $this->registry->get($handlerIdentifier ?? $this->configurationService->getHandlerIdentifier());

        if ($this->shouldBeHidden($content) && !$handler->isHidden($content)) {
            $handler->hide($content);

            $this->logUpdate($content, VisibilityUpdateResult::Hidden);

            return VisibilityUpdateResult::Hidden;
        }

        if ($this->shouldBeVisible($content) && !$handler->isVisible($content)) {
            $handler->reveal($content);

            $this->logUpdate($content, VisibilityUpdateResult::Revealed);

            return VisibilityUpdateResult::Revealed;
        }

        return VisibilityUpdateResult::NoChange;
    }

    /**
     * @throws InvalidStateException
     */
    public function shouldBeHidden(Content $content): bool
    {
        if (!$this->isValid($content)) {
            throw new InvalidStateException($content);
        }

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Field $publishFromField */
        $publishFromField = $content->getField('publish_from');

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Field $publishToField */
        $publishToField = $content->getField('publish_to');

        $publishFromDateTime = $this->getDateTime($publishFromField);
        $publishToDateTime = $this->getDateTime($publishToField);

        if ($publishToDateTime === null && $publishFromDateTime === null) {
            return false;
        }

        $currentDateTime = new DateTime();

        if ($publishFromDateTime !== null && $publishFromDateTime > $currentDateTime) {
            return true;
        }

        if ($publishToDateTime !== null && $publishToDateTime <= $currentDateTime) {
            return true;
        }

        return false;
    }

    /**
     * @throws InvalidStateException
     */
    public function shouldBeVisible(Content $content): bool
    {
        if (!$this->isValid($content)) {
            throw new InvalidStateException($content);
        }

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Field $publishFromField */
        $publishFromField = $content->getField('publish_from');

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Field $publishToField */
        $publishToField = $content->getField('publish_to');

        $publishFromDateTime = $this->getDateTime($publishFromField);
        $publishToDateTime = $this->getDateTime($publishToField);

        if ($publishToDateTime === null && $publishFromDateTime === null) {
            return false;
        }

        $currentDateTime = new DateTime();

        if ($publishFromDateTime === null && $publishToDateTime > $currentDateTime) {
            return true;
        }

        if ($publishToDateTime === null && $publishFromDateTime <= $currentDateTime) {
            return true;
        }

        if ($publishFromDateTime !== null && $publishFromDateTime <= $currentDateTime
            && $publishToDateTime !== null && $publishToDateTime > $currentDateTime) {
            return true;
        }

        return false;
    }

    private function isValid(Content $content): bool
    {
        $publishFromField = $content->getField('publish_from');
        $publishToField = $content->getField('publish_to');

        if ($publishFromField === null || $publishToField === null) {
            return false;
        }

        if (
            ($publishFromField->fieldTypeIdentifier !== 'ibexa_datetime' && $publishFromField->fieldTypeIdentifier !== 'ibexa_date')
            || ($publishToField->fieldTypeIdentifier !== 'ibexa_datetime' && $publishToField->fieldTypeIdentifier !== 'ibexa_date')
        ) {
            return false;
        }

        $publishFromDateTime = $this->getDateTime($publishFromField);
        $publishToDateTime = $this->getDateTime($publishToField);
        if ($publishFromDateTime !== null && $publishToDateTime !== null && $publishToDateTime <= $publishFromDateTime) {
            return false;
        }

        return true;
    }

    private function getDateTime(Field $field): null|DateTime
    {
        if ($field->value instanceof DateValue) {
            return $field->value->date;
        }
        if ($field->value instanceof DateAndTimeValue) {
            return $field->value->value;
        }

        return null;
    }

    private function logUpdate(Content $content, VisibilityUpdateResult $updateResult): void
    {
        $this->logger->info(
            sprintf(
                "Content '%s' with id #%d has been %s.",
                $content->getName(),
                $content->id,
                $updateResult->value,
            ),
        );
    }
}
