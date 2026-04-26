<?php

/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Runner\Action\Cli\Command\Placeholder\Processor;

/**
 * Class Config
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhook-git/captainhook
 * @since   Class available since Release 5.6.0
 */
class Config extends Foundation
{
    /**
     * Maps the config value names to actual methods that have to be called to retrieve the value
     *
     * @var array<string, string>
     */
    private array $valueToMethods = [
        'bootstrap'           => 'getBootstrap',
        'git-directory'       => 'getGitDirectory',
        'php-path'            => 'getPhpPath',
    ];

    /**
     * @param  array<string, mixed> $options
     * @return string
     */
    public function replacement(array $options): string
    {
        if (!isset($options['value-of'])) {
            return '';
        }

        return $this->getConfigValueFor($options['value-of']);
    }

    /**
     * Returns the config value '' by default if value is unknown
     *
     * @param  string $value
     * @return string
     */
    private function getConfigValueFor(string $value): string
    {
        // check if the value should be a custom config value
        if (str_starts_with($value, 'custom>>')) {
            return $this->findCustomConfigValue($value);
        }
        // check if the value should be a plugin config value
        if (str_starts_with($value, 'plugin>>')) {
            return $this->findPluginConfigValue($value);
        }
        // if we don't know what method to call, return an empty string
        if (!isset($this->valueToMethods[$value])) {
            return '';
        }

        $method = $this->valueToMethods[$value];
        return $this->config->$method();
    }

    /**
     * Returns a custom configuration value
     *
     * @param  string $value
     * @return string
     */
    private function findCustomConfigValue(string $value): string
    {
        $key    = substr($value, 8);
        $custom = $this->config->getCustomSettings();
        return $custom[$key] ?? '';
    }

    /**
     * Returns a option value for a plugin configuration
     *
     * @param  string $value
     * @return string
     */
    private function findPluginConfigValue(string $value): string
    {
        [$pluginClass, $key] = explode('.', substr($value, 8));
        foreach ($this->config->getPlugins() as $plugin) {
            if ($plugin->getPlugin() === $pluginClass) {
                return $plugin->getOptions()->get($key, '');
            }
        }
        return '';
    }
}
