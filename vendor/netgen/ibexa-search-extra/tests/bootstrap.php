<?php

declare(strict_types=1);

use Ibexa\Core\Persistence\Legacy\Content\Gateway\DoctrineDatabase;
use Ibexa\Core\Persistence\Legacy\Content\Gateway\DoctrineDatabase\QueryBuilder;
use Ibexa\Core\Repository\ContentService;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Component\HttpFoundation\Request;

// Register ClockMock for Request class before any tests are run
// https://github.com/symfony/symfony/issues/28259
ClockMock::register(Request::class);

// Register ClockMock, as otherwise they are mocked until the first method call.
ClockMock::register(DoctrineDatabase::class);
ClockMock::register(ContentService::class);
ClockMock::register(QueryBuilder::class);

$file = __DIR__ . '/../vendor/autoload.php';

if (!\file_exists($file)) {
    throw new RuntimeException('Install dependencies using composer to run the test suite.');
}

$autoload = require $file;
