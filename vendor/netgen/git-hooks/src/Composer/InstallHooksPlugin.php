<?php

declare(strict_types=1);

namespace Netgen\GitHooks\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

use function file_exists;
use function getcwd;

final class InstallHooksPlugin implements PluginInterface, EventSubscriberInterface
{
    private Composer $composer;

    private IOInterface $io;

    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ScriptEvents::POST_INSTALL_CMD => 'installHooks',
            ScriptEvents::POST_UPDATE_CMD => 'installHooks',
        ];
    }

    public function installHooks(Event $event): void
    {
        if (!$this->hasConfigFile() || !$this->hasGitRepo() || $this->isPluginDisabled()) {
            return;
        }

        $this->runInstallCommand();
    }

    public function deactivate(Composer $composer, IOInterface $io): void {}

    public function uninstall(Composer $composer, IOInterface $io): void {}

    private function runInstallCommand(): void
    {
        $phpFinder = new PhpExecutableFinder();
        $php = $phpFinder->find();

        $binFolder = $this->composer->getConfig()->get('bin-dir');
        $captainHookExecutable = $binFolder . '/captainhook';

        $process = new Process(
            [
                $php,
                $captainHookExecutable,
                'install',
                '--force',
                $this->io->isDecorated() ? '--ansi' : '--no-ansi',
            ],
        );

        $process->run(
            function (string $type, string $line): void {
                $this->io->write($line, false);
            },
        );
    }

    private function hasConfigFile(): bool
    {
        return file_exists(getcwd() . '/captainhook.json');
    }

    private function hasGitRepo(): bool
    {
        return file_exists(getcwd() . '/.git');
    }

    private function isPluginDisabled(): bool
    {
        $extra = $this->composer->getPackage()->getExtra();

        return (bool) ($extra['captainhook']['disable-plugin'] ?? false);
    }
}
