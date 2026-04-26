<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Export;

use DateTimeImmutable;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Core\Helper\TranslationHelper;
use Netgen\InformationCollection\API\Export\ExportResponseFormatter;
use Netgen\InformationCollection\API\Value\Export\Export;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;

use function array_unshift;
use function file_put_contents;
use function json_encode;
use function str_ends_with;

class JsonExportResponseFormatter implements ExportResponseFormatter
{
    private TranslationHelper $translationHelper;
    private SluggerInterface $slugger;

    public function __construct(TranslationHelper $translationHelper, SluggerInterface $slugger)
    {
        $this->translationHelper = $translationHelper;
        $this->slugger = $slugger;
    }

    public function getIdentifier(): string
    {
        return 'json_export';
    }

    public function format(Export $export, Content $content): Response
    {
        $contents = $export->getContents();

        array_unshift($contents, $export->getHeader());

        return new JsonResponse($contents);
    }

    public function formatToFile(Export $export, Content $content, string $path): File
    {
        $contentName = $this->translationHelper->getTranslatedContentName($content);

        $contents = $export->getContents();

        array_unshift($contents, $export->getHeader());

        $contentNameSlug = $this->slugger->slug($contentName)->lower()->toString();
        $path = str_ends_with($path, '/') ? $path : $path . '/';
        $filePath = $path . $contentNameSlug . '-' . (new DateTimeImmutable())->format('YmdHis') . '.json';

        file_put_contents($filePath, json_encode($contents));

        return new File($filePath);
    }
}
