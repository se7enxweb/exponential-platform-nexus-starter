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

/**
 * Manage display settings for the info command
 *
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhook-git/captainhook
 * @since   Class available since Release 5.29.1
 */
final class Settings
{
    /**
     * Option values
     */
    public const OPT_ACTIONS    = 'actions';
    public const OPT_CONDITIONS = 'conditions';
    public const OPT_OPTIONS    = 'options';
    public const OPT_CONFIG     = 'config';
    public const OPT_SETTINGS   = 'settings';

    /**
     * Default display options
     *
     * @var array<string, bool>
     */
    private array $options = [
        self::OPT_ACTIONS    => false,
        self::OPT_CONDITIONS => false,
        self::OPT_OPTIONS    => false,
        self::OPT_CONFIG     => false,
        self::OPT_SETTINGS   => false,
    ];

    /**
     * Set a specific config part to be shown
     *
     * @param  string $option
     * @param  bool   $value
     * @return void
     */
    public function set(string $option, bool $value): void
    {
        $this->options[$option] = $value;
    }

    /**
     * Check if a specific config part should be shown
     *
     * @param string $option
     * @return bool
     */
    public function show(string $option): bool
    {
        return $this->options[$option] ?? false;
    }
}
