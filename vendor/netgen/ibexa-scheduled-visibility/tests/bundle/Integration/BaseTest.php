<?php

declare(strict_types=1);

namespace Netgen\IbexaScheduledVisibility\Tests\Integration;

use DateTime;
use DateTimeZone;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Tests\Integration\Core\Repository\BaseTestCase as APIBaseTest;
use Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\ScheduledVisibilityService;

abstract class BaseTest extends APIBaseTest
{
    public static function provideCases(): iterable
    {
        return [
            [
                [
                    'publish_from' => null,
                    'publish_to' => null,
                ],
                false,
            ],
            [
                [
                    'publish_from' => null,
                    'publish_to' => new DateTime('tomorrow', new DateTimeZone('UTC')),
                ],
                false,
            ],
            [
                [
                    'publish_from' => null,
                    'publish_to' => new DateTime('yesterday', new DateTimeZone('UTC')),
                ],
                true,
            ],
            [
                [
                    'publish_from' => new DateTime('2 days ago', new DateTimeZone('UTC')),
                    'publish_to' => new DateTime('yesterday', new DateTimeZone('UTC')),
                ],
                true,
            ],
            [
                [
                    'publish_from' => new DateTime('yesterday', new DateTimeZone('UTC')),
                    'publish_to' => new DateTime('tomorrow', new DateTimeZone('UTC')),
                ],
                false,
            ],
            [
                [
                    'publish_from' => new DateTime('tomorrow', new DateTimeZone('UTC')),
                    'publish_to' => null,
                ],
                true,
            ],
            [
                [
                    'publish_from' => new DateTime('tomorrow', new DateTimeZone('UTC')),
                    'publish_to' => new DateTime('2 day', new DateTimeZone('UTC')),
                ],
                true,
            ],
        ];
    }

    protected function getScheduledVisibilityService(): ScheduledVisibilityService
    {
        /** @var \Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\ScheduledVisibilityService $service */
        $service = $this->getSetupFactory()->getServiceContainer()->get(ScheduledVisibilityService::class);

        return $service;
    }

    protected function createContentType(): ContentType
    {
        $repository = $this->getRepository();

        $contentTypeService = $repository->getContentTypeService();
        $permissionResolver = $repository->getPermissionResolver();

        $typeCreate = $contentTypeService->newContentTypeCreateStruct('test-scheduled-visibility');
        $typeCreate->mainLanguageCode = 'eng-GB';
        $typeCreate->remoteId = '384b94a1bd6bc06826410e284dd9684887bf56fc';
        $typeCreate->urlAliasSchema = 'url|scheme';
        $typeCreate->nameSchema = 'name|scheme';
        $typeCreate->names = [
            'eng-GB' => 'Test Scheduled Visibility',
        ];
        $typeCreate->creatorId = $this->generateId('user', $permissionResolver->getCurrentUserReference()->getUserId());
        $typeCreate->creationDate = $this->createDateTime();

        $publishFromFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct('publish_from', 'ibexa_date');
        $publishFromFieldCreate->names = [
            'eng-GB' => 'Publish from',
        ];
        $publishFromFieldCreate->position = 1;
        $typeCreate->addFieldDefinition($publishFromFieldCreate);

        $publishToFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct('publish_to', 'ibexa_datetime');
        $publishToFieldCreate->names = [
            'eng-GB' => 'Publish to',
        ];
        $publishToFieldCreate->position = 2;
        $typeCreate->addFieldDefinition($publishToFieldCreate);

        $groups = [
            $contentTypeService->loadContentTypeGroupByIdentifier('Media'),
        ];

        $contentTypeDraft = $contentTypeService->createContentType(
            $typeCreate,
            $groups,
        );

        $contentTypeService->publishContentTypeDraft($contentTypeDraft);
        $contentType = $contentTypeService->loadContentType($contentTypeDraft->id);

        self::assertInstanceOf(
            ContentType::class,
            $contentType,
        );

        return $contentType;
    }

    protected function createContent(?DateTime $publishFrom, ?DateTime $publishTo): Content
    {
        $contentService = $this->getRepository()->getContentService();
        $contentType = $this->createContentType();
        $contentCreate = $contentService->newContentCreateStruct($contentType, 'eng-GB');
        $contentCreate->setField('publish_from', $publishFrom);
        $contentCreate->setField('publish_to', $publishTo);

        $contentCreate->remoteId = 'abcdef0123456789abcdef0123456789';
        $contentCreate->alwaysAvailable = true;

        $locationService = $this->getRepository()->getLocationService();
        $content = $contentService->createContent(
            $contentCreate,
            [$locationService->newLocationCreateStruct(2)],
        );
        $publishedContent = $contentService->publishVersion($content->getVersionInfo());

        self::assertInstanceOf(Content::class, $publishedContent);

        return $publishedContent;
    }
}
