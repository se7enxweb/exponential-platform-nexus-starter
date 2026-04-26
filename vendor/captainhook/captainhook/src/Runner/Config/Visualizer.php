<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Runner\Config;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Hook\Util as HookUtil;
use CaptainHook\App\Runner;
use CaptainHook\App\Runner\Config\Visualizer\Output;
use CaptainHook\App\Runner\Hook\Arg;
use RuntimeException;
use SebastianFeldmann\Git\Repository;

/**
 * Class Visualizer
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhook-git/captainhook
 * @since   Class available since Release 5.29.1
 */
class Visualizer extends Runner\RepositoryAware
{
    /**
     * The hook to display
     *
     * @var array<int, string>
     */
    private array $hooks = [];

    /**
     * Show more detailed information
     * @var bool
     */
    private bool $extensive = false;

    /**
     * Handles what parts of the configuration should be displayed
     *
     * @var \CaptainHook\App\Runner\Config\Visualizer\Settings
     */
    private Visualizer\Settings $settings;

    /**
     * Prints to the terminal
     *
     * @var \CaptainHook\App\Runner\Config\Visualizer\Output
     */
    private Output $output;

    /**
     * Set up the visualizer
     *
     * @param \CaptainHook\App\Console\IO       $io
     * @param \CaptainHook\App\Config           $config
     * @param \SebastianFeldmann\Git\Repository $repository
     */
    public function __construct(IO $io, Config $config, Repository $repository)
    {
        $this->settings = new Visualizer\Settings();
        $this->output   = new Visualizer\Output($io, $repository, $this->settings);
        parent::__construct($io, $config, $repository);
    }

    /**
     * Limit config display to s specific hook
     *
     * @param  string $hook
     * @return static
     * @throws \CaptainHook\App\Exception\InvalidHookName
     */
    public function setHook(string $hook): self
    {
        $arg = new Arg(
            $hook,
            static function (string $hook): bool {
                return !HookUtil::isValid($hook);
            }
        );
        $this->hooks = $arg->hooks();
        return $this;
    }

    /**
     * Set the display setting for a config section (actions, conditions, options)
     *
     * @param  string $name
     * @param  bool   $value
     * @return $this
     */
    public function display(string $name, bool $value): Visualizer
    {
        if ($value) {
            $this->settings->set($name, true);
        }
        return $this;
    }

    /**
     * Show more detailed information
     *
     * @param bool $value
     * @return $this
     */
    public function extensive(bool $value): Visualizer
    {
        $this->extensive = $value;
        return $this;
    }

    /**
     * Executes the Runner
     *
     * @return void
     * @throws \RuntimeException
     */
    public function run(): void
    {
        if (!$this->config->isLoadedFromFile()) {
            throw new RuntimeException('No configuration to read');
        }

        $this->output->printHeader($this->config);
        $this->displaySettings();
        $this->displayHooks();
    }

    /**
     * Display the application settings
     *
     * @return void
     */
    private function displaySettings(): void
    {
        if (!$this->settings->show(Visualizer\Settings::OPT_SETTINGS)) {
            return;
        }

        $this->output->printSettings($this->config);
        $this->output->printCustomSettings($this->config->getCustomSettings());
        $this->output->printRunSettings($this->config->getRunConfig());
        $this->output->printPlugins($this->config->getPlugins());
    }

    /**
     * Display the hook configuration
     *
     * @return void
     */
    private function displayHooks(): void
    {
        if ($this->isApplicationSettingsOnly()) {
            return;
        }
        $this->output->printHooks($this->hooksToDisplay(), $this->extensive);
    }

    /**
     * Returns all Hook Configs that should be displayed
     *
     * @return array<\CaptainHook\App\Config\Hook>
     */
    private function hooksToDisplay(): array
    {
        $hooks = [];
        foreach ($this->config->getHookConfigs() as $hookConfig) {
            if ($this->shouldHookBeDisplayed($hookConfig->getName())) {
                $hooks[] = $hookConfig;
            }
        }
        return $hooks;
    }

    /**
     * Check if a hook should be displayed
     *
     * @param string $name
     * @return bool
     */
    private function shouldHookBeDisplayed(string $name): bool
    {
        if (empty($this->hooks)) {
            return true;
        }
        return in_array($name, $this->hooks);
    }

    /**
     * Check if only the application settings should be displayed
     *
     * @return bool
     */
    private function isApplicationSettingsOnly(): bool
    {
        // no app config to display, so clearly no
        if (!$this->settings->show(Visualizer\Settings::OPT_SETTINGS)) {
            return false;
        }

        // check if any action-related stuff has to be shown
        $actionRelatedStuff = [
            Visualizer\Settings::OPT_ACTIONS,
            Visualizer\Settings::OPT_CONDITIONS,
            Visualizer\Settings::OPT_OPTIONS,
            Visualizer\Settings::OPT_CONFIG,
        ];
        // if so app config is not the only thing to display
        foreach ($actionRelatedStuff as $option) {
            if ($this->settings->show($option)) {
                return false;
            }
        }
        // if we display a specific hook showing the actions makes sense
        if (!empty($this->hooks)) {
            return false;
        }
        // otherwise we display only the app config
        return true;
    }
}
