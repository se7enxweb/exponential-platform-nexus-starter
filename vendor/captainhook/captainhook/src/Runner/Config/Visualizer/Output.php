<?php

/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Runner\Config\Visualizer;

use CaptainHook\App\CH;
use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use SebastianFeldmann\Git\Repository;

/**
 * Generates the output to display a captainhook configuration
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhook-git/captainhook
 * @since   Class available since Release 5.29.1
 */
final class Output
{
    /**
     * Handling terminal IO
     *
     * @var \CaptainHook\App\Console\IO
     */
    private IO $io;

    /**
     * Git Repository
     *
     * @var \SebastianFeldmann\Git\Repository
     */
    private Repository $repository;

    /**
     * What parts should be displayed
     *
     * @var \CaptainHook\App\Runner\Config\Visualizer\Settings
     */
    private Settings $settings;

    /**
     * Constructor
     *
     * @param \CaptainHook\App\Console\IO                        $io
     * @param \SebastianFeldmann\Git\Repository                  $repository
     * @param \CaptainHook\App\Runner\Config\Visualizer\Settings $settings
     */
    public function __construct(IO $io, Repository $repository, Settings $settings)
    {
        $this->io         = $io;
        $this->repository = $repository;
        $this->settings   = $settings;
    }

    /**
     * Print header
     *
     * @param  \CaptainHook\App\Config     $config
     * @return void
     */
    public function printHeader(Config $config): void
    {
        $this->io->write([
            '<info>CaptainHook</info> version <comment>' . CH::VERSION . '</comment> ' . CH::RELEASE_DATE,
            '',
            'Configuration File: <fg=cyan>' . $config->getPath() . '</>',
            ''
        ]);
    }

    /**
     * Print basic settings
     *
     * @param  \CaptainHook\App\Config $config
     * @return void
     */
    public function printSettings(Config $config): void
    {
        $this->io->write([
            '<fg=magenta>Config:</>',
            '  <fg=green>Basic Settings:</>',
            '    - <fg=cyan>Verbosity:</fg=cyan> ' . $config->getVerbosity(),
            '    - <fg=cyan>Use colors:</fg=cyan> ' . Util::yesOrNo($config->useAnsiColors()),
            '    - <fg=cyan>Allow failures:</fg=cyan> ' . Util::yesOrNo($config->isFailureAllowed()),
            '    - <fg=cyan>Git directory:</fg=cyan> ' . $config->getGitDirectory(),
            '    - <fg=cyan>Bootstrap file:</fg=cyan> ' . $config->getBootstrap(),
            '    - <fg=cyan>Install mode:</fg=cyan> ' . $config->getRunConfig()->getMode(),
        ]);
    }

    /**
     * Print the run settings
     *
     * @param  \CaptainHook\App\Config\Run $config
     * @return void
     */
    public function printRunSettings(Config\Run $config): void
    {
        $this->io->write(
            [
                '  <fg=green>Run Settings:</>',
                '    - <fg=cyan>mode</>: ' . $config->getMode(),
                '    - <fg=cyan>docker-command</>: ' . $config->getDockerCommand(),
                '    - <fg=cyan>path-captain</>: ' . $config->getCaptainsPath(),
                '    - <fg=cyan>path-git</>: ' . $config->getGitPath(),
            ]
        );
    }

    /**
     * Print custom settings
     *
     * @param  array<string, mixed> $customSettings
     * @return void
     */
    public function printCustomSettings(array $customSettings): void
    {
        if (count($customSettings) === 0) {
            return;
        }
        $this->io->write('  <fg=green>Custom Settings:</>');
        foreach ($customSettings as $key => $value) {
            $this->io->write('    - <fg=cyan>' . $key . '</>: ' . $value);
        }
    }

    /**
     * Print plugins configs
     *
     * @param  array<string, Config\Plugin> $plugins
     * @return void
     */
    public function printPlugins(array $plugins): void
    {
        if (count($plugins) === 0) {
            return;
        }
        $this->io->write('  <fg=green>Plugins:</>');
        foreach ($plugins as $plugin) {
            $this->io->write('    - <fg=cyan>' . $plugin->getPlugin() . '</>');
            $this->printOptions($plugin->getOptions(), '  ');
        }
    }

    /**
     * Print all hook configs
     *
     * @param  array<\CaptainHook\App\Config\Hook> $hooks
     * @param  bool                                $extensive
     * @return void
     */
    public function printHooks(array $hooks, bool $extensive = false): void
    {
        $this->io->write('<fg=magenta>Hooks:</>');
        foreach ($hooks as $hookConfig) {
            $this->printHook($hookConfig, $extensive);
        }
    }

    /**
     * Print a single hook config
     *
     * @param  \CaptainHook\App\Config\Hook $config
     * @param  bool                         $extensive
     * @return void
     */
    public function printHook(Config\Hook $config, bool $extensive): void
    {
        $this->io->write('  <info>' . $config->getName() . '</info>', !$extensive);
        $this->printExtended($config, $extensive);
        $this->printActions($config);
    }

    /**
     * Print extended hook information
     *
     * @param \CaptainHook\App\Config\Hook $config
     * @return void
     */
    public function printExtended(Config\Hook $config, bool $on): void
    {
        if (!$on) {
            return;
        }
        $this->io->write(
            ' ' . str_repeat('-', 50 - strlen($config->getName())) .
            '--[enabled: ' . Util::yesOrNo($config->isEnabled()) .
            ', installed: ' . Util::yesOrNo($this->repository->hookExists($config->getName())) . ']'
        );
    }

    /**
     * Print all actions
     *
     * @param  \CaptainHook\App\Config\Hook $config
     * @return void
     */
    public function printActions(Config\Hook $config): void
    {
        foreach ($config->getActions() as $action) {
            $this->printAction($action);
        }
    }

    /**
     * Print a single Action
     *
     * @param \CaptainHook\App\Config\Action $action
     * @return void
     */
    public function printAction(Config\Action $action): void
    {
        $this->io->write('    - <fg=cyan>' . $action->getLabel() . '</>');
        if ($action->hasLabel() && $this->settings->show(Settings::OPT_ACTIONS)) {
            $this->io->write('      <fg=gray>' . $action->getAction() . '</>');
        }
        $this->printOptions($action->getOptions());
        $this->printActionConfig($action);
        $this->printConditions($action->getConditions());
    }

    /**
     * Print all conditions
     *
     * @param array<\CaptainHook\App\Config\Condition> $conditions
     * @param string                                   $prefix
     * @return void
     */
    public function printConditions(array $conditions, string $prefix = ''): void
    {
        if (empty($conditions)) {
            return;
        }
        if (!$this->settings->show(Settings::OPT_CONDITIONS)) {
            return;
        }

        if (empty($prefix)) {
            $this->io->write($prefix . '      <comment>Conditions:</comment>');
        }
        foreach ($conditions as $condition) {
            $this->printCondition($condition, $prefix);
        }
    }

    /**
     * Print a single Condition
     *
     * @param \CaptainHook\App\Config\Condition $condition
     * @param string                            $prefix
     * @return void
     */
    public function printCondition(Config\Condition $condition, string $prefix = ''): void
    {
        $this->io->write($prefix . '      - <fg=cyan>' . $condition->getExec() . '</>');

        // handle logic conditions
        if (in_array(strtoupper($condition->getExec()), ['OR', 'AND'])) {
            $conditions = [];
            foreach ($condition->getArgs() as $conf) {
                $conditions[] = new Config\Condition($conf['exec'], $conf['args'] ?? []);
            }
            $this->printConditions($conditions, $prefix . '  ');
            return;
        }
        if ($this->settings->show(Settings::OPT_OPTIONS)) {
            if (empty($condition->getArgs())) {
                return;
            }
            $this->io->write($prefix . '        <comment>Args:</comment>');
            foreach ($condition->getArgs() as $key => $value) {
                $this->printOption($key, $value, $prefix . '   ');
            }
        }
    }

    /**
     * Print all options
     *
     * @param  \CaptainHook\App\Config\Options $options
     * @param  string                          $prefix
     * @return void
     */
    public function printOptions(Config\Options $options, string $prefix = ''): void
    {
        if (empty($options->getAll())) {
            return;
        }
        if (!$this->settings->show(Settings::OPT_OPTIONS)) {
            return;
        }

        $this->io->write($prefix . '      <comment>Options:</comment>');
        foreach ($options->getAll() as $key => $value) {
            $this->printOption($key, $value, $prefix);
        }
    }

    /**
     * Print a single option
     *
     * @param  string                      $key
     * @param  mixed                       $value
     * @param  string                      $prefix
     * @return void
     */
    public function printOption(string $key, mixed $value, string $prefix = ''): void
    {
        if (is_array($value)) {
            $value = implode(', ', $value);
        }
        $this->io->write(
            $prefix . '        - ' . $key . ': <fg=gray>' .
            Util::escapeLineBreaks($value) .
            '</>'
        );
    }

    /**
     * Print the action config settings
     *
     * @param  \CaptainHook\App\Config\Action $action
     * @param  string                         $prefix
     * @return void
     */
    public function printActionConfig(Config\Action $action, string $prefix = ''): void
    {
        if (!$this->settings->show(Settings::OPT_CONFIG)) {
            return;
        }

        $config = [];
        if ($action->getLabel() != $action->getAction()) {
            $config['label'] = $action->getLabel();
        }
        if ($action->isFailureAllowed()) {
            $config['failureAllowed'] = true;
        }
        if (empty($config)) {
            return;
        }
        $this->io->write('      <comment>Config:</comment>');
        foreach ($config as $key => $value) {
            $this->io->write(
                '        - ' . $key . ': <fg=gray>' .
                Util::escapeLineBreaks((string)$value) .
                '</>'
            );
        }
    }
}
