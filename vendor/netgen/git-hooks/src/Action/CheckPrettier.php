<?php

declare(strict_types=1);

namespace Netgen\GitHooks\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Config\Action as ActionConfig;
use CaptainHook\App\Console\IO;
use SebastianFeldmann\Cli\Processor\ProcOpen as Processor;
use SebastianFeldmann\Git\Repository;
use Symfony\Component\Finder\Finder;

use function escapeshellarg;
use function preg_filter;
use function preg_match;
use function sprintf;

final class CheckPrettier extends Action
{
    protected const ERROR_MESSAGE = 'Committed code was not formatted correctly. Please check the output for suggested diff.';

    protected function doExecute(Config $config, IO $io, Repository $repository, ActionConfig $action): void
    {
        /** @var string|string[] $extensions */
        $extensions = $action->getOptions()->get('extensions', ['js', 'jsx', 'ts', 'tsx', 'css', 'scss']);
        $excludedFiles = $action->getOptions()->get('excluded_files') ?? [];
        $directories = $action->getOptions()->get('directories', ['assets']);
        $prettierCommand = $action->getOptions()->get('prettier_command', 'pnpm prettier');
        $formatOptions = $action->getOptions()->get('prettier_options', '--check');

        $finder = new Finder();
        preg_filter('/^/', '*.', $extensions);
        $finder->in($directories)->files()->name($extensions);

        if ($finder->hasResults()) {
            $io->write(sprintf('Running %s on files:', $prettierCommand));

            foreach ($finder as $file) {
                if ($this->shouldSkipFileCheck($file->getPath(), $excludedFiles)) {
                    continue;
                }

                $result = $this->checkPrettier($file->getPath(), $prettierCommand, $formatOptions);

                $io->write(sprintf('<info>%s: </info>', $file->getPath()));

                /** @var bool $isResultSuccess */
                $isResultSuccess = $result['success'];

                if ($isResultSuccess) {
                    $io->write($result['output']);
                } else {
                    $io->writeError(sprintf('<error>%s</error>', $result['error']));
                }

                if ($result['success'] !== true) {
                    $this->throwError($action, $io);
                }
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
    protected function checkPrettier(string $file, string $prettierCommand, string $prettierOptions): array
    {
        $process = new Processor();
        $result = $process->run($prettierCommand . ' ' . $prettierOptions . '  ' . escapeshellarg($file));

        return [
            'success' => $result->isSuccessful(),
            'output' => $result->getStdOut(),
            'error' => $result->getStdErr(),
        ];
    }
}
