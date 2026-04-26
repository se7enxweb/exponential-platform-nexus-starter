<?php

/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Runner\Action\Cli\Command;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Hooks;
use CaptainHook\App\Runner\Action\Cli\Command\Placeholder\Processor;
use CaptainHook\App\Runner\Action\Cli\Command\Placeholder\Processor\Arg;
use SebastianFeldmann\Git\Repository;

/**
 * Class Formatter
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhook-git/captainhook
 * @since   Class available since Release 5.0.0
 */
class Formatter
{
    /**
     * Cache storage for computed placeholder values
     *
     * @var array<string, string>
     */
    private static array $cache = [];

    /**
     * Input output handler
     *
     * @var \CaptainHook\App\Console\IO
     */
    private IO $io;

    /**
     * CaptainHook configuration
     *
     * @var \CaptainHook\App\Config
     */
    private Config $config;

    /**
     * List of available placeholders
     *
     * @var array<string, string>
     */
    private static array $placeholders = [
        'arg'           => Processor\Arg::class,
        'config'        => Processor\Config::class,
        'env'           => Processor\Env::class,
        'staged_files'  => Processor\StagedFiles::class,
        'changed_files' => Processor\ChangedFiles::class,
        'branch_files'  => Processor\BranchFiles::class,
        'stdin'         => Processor\StdIn::class,
    ];

    /**
     * Previously used placeholders to replace arguments
     *
     * @var array<string, string>
     */
    private static array $legacyPlaceHolder = [
        'FILE'         => Hooks::ARG_MESSAGE_FILE,
        'GITCOMMAND'   => Hooks::ARG_GIT_COMMAND,
        'HASH'         => Hooks::ARG_HASH,
        'MODE'         => Hooks::ARG_MODE,
        'NEWHEAD'      => Hooks::ARG_NEW_HEAD,
        'PREVIOUSHEAD' => Hooks::ARG_PREVIOUS_HEAD,
        'SQUASH'       => Hooks::ARG_SQUASH,
        'TARGET'       => Hooks::ARG_TARGET,
        'URL'          => Hooks::ARG_URL,
    ];

    /**
     * Git repository
     *
     * @var \SebastianFeldmann\Git\Repository
     */
    private Repository $repository;

    /**
     * Formatter constructor
     *
     * @param \CaptainHook\App\Console\IO       $io
     * @param \CaptainHook\App\Config           $config
     * @param \SebastianFeldmann\Git\Repository $repository
     */
    public function __construct(IO $io, Config $config, Repository $repository)
    {
        $this->io         = $io;
        $this->config     = $config;
        $this->repository = $repository;
    }

    /**
     * Replaces all placeholders in a cli command
     *
     * @param  string $command
     * @return string
     */
    public function format(string $command): string
    {
        // find all replacements {SOMETHING}
        $placeholders = $this->findAllPlaceholders($command);
        foreach ($placeholders as $placeholder) {
            $command = str_replace('{$' . $placeholder . '}', $this->replace($placeholder), $command);
        }

        return $command;
    }

    /**
     * Returns al list of all placeholders
     *
     * @param  string $command
     * @return array<int, string>
     */
    private function findAllPlaceholders(string $command): array
    {
        $placeholders = [];
        $matches      = [];

        if (preg_match_all('#{\$([a-z_]+(\|[a-z\-]+:.*)?)}#iU', $command, $matches)) {
            foreach ($matches[1] as $match) {
                $placeholders[] = $match;
            }
        }

        return $placeholders;
    }

    /**
     * Return a given placeholder value
     *
     * @param  string $placeholderRaw
     * @return string
     */
    private function replace(string $placeholderRaw): string
    {
        // if the placeholder references an original hook argument, set up the real placeholder
        // {$FILE} => ARG|value-of:message-file
        if (array_key_exists($placeholderRaw, self::$legacyPlaceHolder)) {
            $argument       = self::$legacyPlaceHolder[$placeholderRaw];
            $placeholderRaw = 'ARG|value-of:' . Arg::toPlaceholder($argument);
        }
        // create the placeholder
        $placeholder = new Placeholder($placeholderRaw);
        // make sure it is allowed
        if (!$this->isPlaceholderValid($placeholder->name())) {
            return '';
        }
        // compute the placeholder value
        $replacement = $this->computedPlaceholder($placeholder);
        $this->write($placeholder, $replacement);
        return $replacement;
    }

    /**
     * Compute the placeholder value
     *
     * @param  \CaptainHook\App\Runner\Action\Cli\Command\Placeholder $placeholder
     * @return string
     */
    private function computedPlaceholder(Placeholder $placeholder): string
    {
        // to not compute the same placeholder multiple times
        if (!$this->isCached($placeholder->key())) {
            $processor   = $this->createProcessor($placeholder->name());
            $replacement = $processor->replacement($placeholder->options());

            if (!$placeholder->isCacheable()) {
                return $replacement;
            }
            $this->cache($placeholder->key(), $replacement);
        }
        return $this->cached($placeholder->key());
    }

    /**
     * Placeholder factory method
     *
     * @param  string $placeholder
     * @return \CaptainHook\App\Runner\Action\Cli\Command\Placeholder\Processor
     */
    private function createProcessor(string $placeholder): Processor
    {
        /** @var class-string<\CaptainHook\App\Runner\Action\Cli\Command\Placeholder\Processor> $class */
        $class = self::$placeholders[$placeholder];
        return new $class($this->io, $this->config, $this->repository);
    }

    /**
     * Checks if a placeholder is available for computation
     *
     * @param  string $placeholder
     * @return bool
     */
    private function isPlaceholderValid(string $placeholder): bool
    {
        return isset(self::$placeholders[$placeholder]);
    }

    /**
     * Check if a placeholder is cached already
     *
     * @param  string $placeholder
     * @return bool
     */
    private static function isCached(string $placeholder): bool
    {
        return isset(self::$cache[$placeholder]);
    }

    /**
     * Cache a given placeholder value
     *
     * @param string $placeholder
     * @param string $replacement
     */
    private static function cache(string $placeholder, string $replacement): void
    {
        self::$cache[$placeholder] = $replacement;
    }

    /**
     * Return cached value for given placeholder
     *
     * @param  string $placeholder
     * @return string
     */
    private static function cached(string $placeholder): string
    {
        return self::$cache[$placeholder] ?? '';
    }

    /**
     * Write some verbose placeholder output
     *
     * @param  \CaptainHook\App\Runner\Action\Cli\Command\Placeholder $placeholder
     * @param  string                                                 $replacement
     * @return void
     */
    private function write(Placeholder $placeholder, string $replacement): void
    {
        $this->io->write(
            [
                '  <fg=cyan>Placeholder:</> ' . $placeholder->name(),
                '    options: <fg=gray>' .  $this->optionsAsString($placeholder->options()) . '</>',
                '    replacement: <fg=gray>' . $replacement . '</>',
            ],
            true,
            IO::VERBOSE
        );
    }

    /**
     * Generate the options output string
     *
     * ['foo' => 'bar'] => '(foo:bar)'
     *
     * @param  array<string, string> $options
     * @return string
     */
    private function optionsAsString(array $options): string
    {
        $optionsStrings = [];
        foreach ($options as $name => $value) {
            $optionsStrings[] = $name . ':' . $value;
        }
        return empty($options) ? '' : ' (' . implode(', ', $optionsStrings) . ')';
    }
}
