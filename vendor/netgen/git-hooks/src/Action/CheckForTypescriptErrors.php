<?php

declare(strict_types=1);

namespace Netgen\GitHooks\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Config\Action as ActionConfig;
use CaptainHook\App\Console\IO;
use SebastianFeldmann\Cli\Processor\ProcOpen as Processor;
use SebastianFeldmann\Git\Repository;

use function escapeshellarg;
use function sprintf;

final class CheckForTypescriptErrors extends Action
{
    protected const ERROR_MESSAGE = 'Committed code has typescript errors. Please check the output for suggested diff.';

    protected function doExecute(Config $config, IO $io, Repository $repository, ActionConfig $action): void
    {
        $configDirs = $action->getOptions()->get('config_dirs', ['.']);
        $typescriptCompilerCommand = $action->getOptions()->get('typescript_compiler_command', 'npx tsc');
        $typescriptCompilerOptions = $action->getOptions()->get('typescript_compiler_options', '--noEmit');

        $io->write(sprintf('Running %s on files:', $typescriptCompilerCommand), true, IO::VERBOSE);
        foreach ($configDirs as $dir) {
            $io->write(sprintf('  - %s', $dir), true, IO::VERBOSE);

            $result = $this->checkTypescriptErrors($dir, $typescriptCompilerCommand, $typescriptCompilerOptions);
            $io->write($result['output']);

            if ($result['success'] !== true) {
                $this->throwError($action, $io);
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function checkTypescriptErrors(string $dir, string $typescriptCompilerCommand, string $typescriptCompilerOptions): array
    {
        $process = new Processor();
        $result = $process->run($typescriptCompilerCommand . '  ' . $typescriptCompilerOptions . '  --project ' . escapeshellarg($dir));

        return [
            'success' => $result->isSuccessful(),
            'output' => $result->getStdOut(),
        ];
    }
}
