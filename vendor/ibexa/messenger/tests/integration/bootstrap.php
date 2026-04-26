<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

use Ibexa\Contracts\Core\Test\Persistence\Fixture\FixtureImporter;
use Ibexa\Contracts\Test\Core\IbexaTestKernel;
use Ibexa\Tests\Core\Repository\LegacySchemaImporter;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpKernel\Kernel;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

chdir(dirname(__DIR__, 2));

$kernel = (static function (): IbexaTestKernel {
    if (!isset($_SERVER['KERNEL_CLASS']) && !isset($_ENV['KERNEL_CLASS'])) {
        throw new LogicException(
            'You must set the KERNEL_CLASS environment variable to the fully-qualified class name of your Kernel'
            . ' in phpunit.xml / phpunit.xml.dist.',
        );
    }

    $class = (string)($_ENV['KERNEL_CLASS'] ?? $_SERVER['KERNEL_CLASS']);
    if (!class_exists($class)) {
        throw new RuntimeException(sprintf(
            'Class "%s" doesn\'t exist or cannot be autoloaded.'
            . ' Check that the KERNEL_CLASS value in phpunit.xml matches the fully-qualified class name of your Kernel.',
            $class,
        ));
    }

    if (!is_a($class, IbexaTestKernel::class, true)) {
        throw new RuntimeException(sprintf('Class "%s" is not a "%s".', $class, Kernel::class));
    }

    return new $class('test', true);
})();
$kernel->boot();

$application = new Application($kernel);
$application->setAutoExit(false);

$databaseUrl = getenv('DATABASE_URL');
if ($databaseUrl !== false && 'sqlite' !== substr($databaseUrl, 0, 6)) {
    $application->run(new ArrayInput([
        'command' => 'doctrine:database:drop',
        '--if-exists' => '1',
        '--force' => '1',
        '--quiet' => true,
    ]));
}

$application->run(new ArrayInput([
    'command' => 'doctrine:database:create',
    '--quiet' => true,
]));

$application->run(new ArrayInput([
    'command' => 'doctrine:schema:update',
    '--em' => 'ibexa_default',
    '--force' => true,
    '--quiet' => true,
]));

/** @var \Psr\Container\ContainerInterface $testContainer */
$testContainer = $kernel->getContainer()->get('test.service_container');

$schemaImporter = $testContainer->get(LegacySchemaImporter::class);
foreach ($kernel->getSchemaFiles() as $file) {
    $schemaImporter->importSchema($file);
}

$fixtureImporter = $testContainer->get(FixtureImporter::class);
foreach ($kernel->getFixtures() as $fixture) {
    $fixtureImporter->import($fixture);
}

/** @var \Ibexa\Contracts\Core\Search\VersatileHandler $handler */
$handler = $testContainer->get('ibexa.spi.search');
$handler->purgeIndex();

$kernel->shutdown();
