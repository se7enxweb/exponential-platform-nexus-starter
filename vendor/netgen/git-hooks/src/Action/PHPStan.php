<?php

declare(strict_types=1);

namespace Netgen\GitHooks\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use SebastianFeldmann\Cli\Processor\ProcOpen as Processor;
use SebastianFeldmann\Git\Repository;

use function count;
use function escapeshellarg;
use function preg_match;
use function str_replace;

final class PHPStan extends Action
{
    protected const ERROR_MESSAGE = 'Committed PHP code did not pass phpstan inspection. Please check the output for errors.';

    protected function doExecute(Config $config, IO $io, Repository $repository, Config\Action $action): void
    {
        $changedPHPFiles = $repository->getIndexOperator()->getStagedFilesOfType('php');
        if (count($changedPHPFiles) === 0) {
            return;
        }

        $excludedFiles = $action->getOptions()->get('excluded_files') ?? [];

        $io->write('Running phpstan on files:', true, IO::VERBOSE);
        foreach ($changedPHPFiles as $file) {
            if ($this->shouldSkipFileCheck($file, $excludedFiles)) {
                continue;
            }

            $result = $this->analyzeFile($file, $config, $action);

            $io->write($result['output']);

            if ($result['success'] !== true) {
                $this->throwError($action, $io);
            }
        }
    }

    /**
     * @param string[] $excludedFiles
     */
    protected function shouldSkipFileCheck(string $file, array $excludedFiles): bool
    {
        foreach ($excludedFiles as $excludedFile) {
            // File definition using regexp
            if ($excludedFile[0] === '/') {
                if (preg_match($excludedFile, $file) === 1) {
                    return true;
                }

                continue;
            }
            if ($excludedFile === $file) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<string, mixed>
     */
    protected function analyzeFile(string $file, Config $config, Config\Action $action): array
    {
        $process = new Processor();

        $result = $process->run($this->getCommand($file, $config, $action));

        return [
            'success' => $result->isSuccessful(),
            'output' => $result->getStdOut(),
        ];
    }

    protected function getCommand(string $file, Config $config, Config\Action $action): string
    {
        $command = $action->getOptions()->get('command');

        if ($command !== null) {
            return str_replace('%file%', escapeshellarg($file), $command);
        }

        $phpstanPath = $action->getOptions()->get('phpstan_path') ?? 'vendor/bin/phpstan';
        $level = $action->getOptions()->get('level') ?? 8;

        return $config->getPhpPath() . ' ' . $phpstanPath . ' analyse --level=' . $level . ' ' . escapeshellarg($file);
    }
}
