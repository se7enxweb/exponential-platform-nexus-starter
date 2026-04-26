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

final class CheckLinter extends Action
{
    protected const ERROR_MESSAGE = 'Committed JS code did not pass linter. Please check the output for suggested diff.';

    protected function doExecute(Config $config, IO $io, Repository $repository, ActionConfig $action): void
    {
        $directories = $action->getOptions()->get('directories', ['assets']);
        $linterCommand = $action->getOptions()->get('linter_command', 'pnpm eslint');

        $io->write(sprintf('Running %s on files:', $linterCommand), true, IO::VERBOSE);

        $result = $this->checkLinter($directories, $linterCommand);
        $io->write($result['output']);

        if ($result['success'] !== true) {
            $this->throwError($action, $io);
        }
    }

    /**
     * @param string[] $directories
     *
     * @return array<string, mixed>
     */
    protected function checkLinter(array $directories, string $linterCommand): array
    {
        $process = new Processor();
        $cliString = $linterCommand;

        foreach ($directories as $directory) {
            $cliString .= '  ' . escapeshellarg($directory);
        }

        $result = $process->run($cliString);

        return [
            'success' => $result->isSuccessful(),
            'output' => $result->getStdOut(),
        ];
    }
}
