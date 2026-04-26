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

use CaptainHook\App\Console\IOUtil;

/**
 * Class Placeholder
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhook-git/captainhook
 * @since   Class available since Release 5.29.0
 */
class Placeholder
{
    /**
     * Placeholder key
     */
    private string $raw;

    /**
     * Name of the placeholder
     */
    private string $name;

    /**
     * Configured options
     *
     * @var array<string, string>
     */
    private array $options;

    /**
     * Formatter constructor
     *
     * @param string $configValue
     */
    public function __construct(string $configValue)
    {
        $this->raw     = $configValue;
        $parts         = explode('|', $this->raw);
        $this->name    = strtolower($parts[0]);
        $this->options = $this->parseOptions(array_slice($parts, 1));
    }

    /**
     * Returns a mappable string representation of the placeholder
     *
     * @return string
     */
    public function key(): string
    {
        return $this->raw;
    }

    /**
     * Return the placeholder name
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Check for an option
     *
     * @param  string $name
     * @return bool
     */
    public function hasOption(string $name): bool
    {
        return isset($this->options[$name]);
    }

    /**
     * Return an option
     *
     * @param  string $name
     * @param  string $default
     * @return string
     */
    public function option(string $name, string $default = ''): string
    {
        return $this->options[$name] ?? $default;
    }

    /**
     * Return all options
     *
     * @return array<string, string>
     */
    public function options(): array
    {
        return $this->options;
    }

    /**
     * Should the placeholder be cached?
     *
     * @return bool
     */
    public function isCacheable(): bool
    {
        if ($this->hasOption('cache')) {
            return IOUtil::stringToBool($this->option('cache'), true);
        }
        return true;
    }

    /**
     * Parse options from ["name:'value'", "name:'value'"] to ["name" => "value", "name" => "value"]
     *
     * @param  array<int, string> $raw
     * @return array<string, string>
     */
    private function parseOptions(array $raw): array
    {
        $options = [];
        foreach ($raw as $rawOption) {
            $matches = [];
            if (preg_match('#^([a-z_\-]+):(.*)?$#i', $rawOption, $matches)) {
                $options[strtolower($matches[1])] = $matches[2] ?? '';
            }
        }
        return $options;
    }
}
