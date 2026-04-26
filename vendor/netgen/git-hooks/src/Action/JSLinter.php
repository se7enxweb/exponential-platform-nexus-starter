<?php

declare(strict_types=1);

namespace Netgen\GitHooks\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use SebastianFeldmann\Cli\Processor\ProcOpen as Processor;
use SebastianFeldmann\Git\Repository;

use function array_merge;
use function count;
use function escapeshellarg;
use function preg_match;
use function sprintf;

final class JSLinter extends Action
{
    protected const ERROR_MESSAGE = 'Committed JS code did not pass linter. Please check the output for suggested diff.';

    protected function doExecute(Config $config, IO $io, Repository $repository, Config\Action $action): void
    {
        $extensions = $action->getOptions()->get('extensions', ['js']);

        $changedFiles = [];
        foreach ($extensions as $extension) {
            $changedFiles = array_merge($changedFiles, $repository->getIndexOperator()->getStagedFilesOfType($extension));
        }

        if (count($changedFiles) === 0) {
            return;
        }

        $excludedFiles = $action->getOptions()->get('excluded_files') ?? [];

        $linterCommand = $action->getOptions()->get('linter_command', 'yarn eslint');
        $linterOptions = $action->getOptions()->get('linter_options', '--fix-dry-run');

        $io->write('Running linter on files:', true, IO::VERBOSE);
        foreach ($changedFiles as $file) {
            if ($this->shouldSkipFileCheck($file, $excludedFiles)) {
                continue;
            }

            $result = $this->lintFile($file, $linterCommand, $linterOptions);

            if ($result['success'] === true) {
                $io->write($result['output']);
            } else {
                $io->writeError(sprintf('<error>%s</error>', $result['error']));
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
    protected function lintFile(string $file, string $linterCommand, string $linterOptions): array
    {
        $process = new Processor();
        $result = $process->run($linterCommand . ' ' . $linterOptions . '  ' . escapeshellarg($file));

        return [
            'success' => $result->isSuccessful(),
            'output' => $result->getStdOut(),
            'error' => $result->getStdErr(),
        ];
    }
}
