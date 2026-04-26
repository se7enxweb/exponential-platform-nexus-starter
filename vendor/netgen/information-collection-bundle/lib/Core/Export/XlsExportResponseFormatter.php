<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\Export;

use DateTimeImmutable;
use Exception;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Core\Helper\TranslationHelper;
use Netgen\InformationCollection\API\Export\ExportResponseFormatter;
use Netgen\InformationCollection\API\Value\Export\Export;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;

use function header;
use function mb_substr;
use function str_ends_with;
use function str_replace;

final class XlsExportResponseFormatter implements ExportResponseFormatter
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
        return 'phpexcel_xls_export';
    }

    public function format(Export $export, Content $content): Response
    {
        /**
         * Ensure valid filename.
         *
         * @see vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Worksheet/Worksheet.php:66
         */
        $contentName = $this->translationHelper->getTranslatedContentName($content);
        $contentName = str_replace(['*', ':', '/', '\\', '?', '[', ']'], '-', $contentName);

        $spreadsheet = new Spreadsheet();
        $activeSheet = $spreadsheet->getActiveSheet();

        try {
            $activeSheet->setTitle(mb_substr($contentName, 0, 30));
        } catch (Exception) {
            $activeSheet->setTitle('Information collection export');
        }

        $activeSheet->fromArray($export->getHeader(), null, 'A1', true);
        $activeSheet->fromArray($export->getContents(), null, 'A2', true);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $contentName . '.xls"');
        header('Cache-Control: max-age=0');

        $writer = new Xls($spreadsheet);
        $writer->save('php://output');

        return new Response('');
    }

    public function formatToFile(Export $export, Content $content, string $path): File
    {
        /**
         * Ensure valid filename.
         *
         * @see vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Worksheet/Worksheet.php:66
         */
        $contentName = $this->translationHelper->getTranslatedContentName($content);
        $contentName = str_replace(['*', ':', '/', '\\', '?', '[', ']'], '-', $contentName);

        $spreadsheet = new Spreadsheet();
        $activeSheet = $spreadsheet->getActiveSheet();

        try {
            $activeSheet->setTitle(mb_substr($contentName, 0, 30));
        } catch (Exception) {
            $activeSheet->setTitle('Information collection export');
        }

        $activeSheet->fromArray($export->getHeader(), null, 'A1', true);
        $activeSheet->fromArray($export->getContents(), null, 'A2', true);

        $writer = new Xls($spreadsheet);

        $contentNameSlug = $this->slugger->slug($contentName)->lower()->toString();
        $path = str_ends_with($path, '/') ? $path : $path . '/';
        $filePath = $path . $contentNameSlug . '-' . (new DateTimeImmutable())->format('YmdHis') . '.xls';

        $writer->save($filePath);

        return new File($filePath);
    }
}
