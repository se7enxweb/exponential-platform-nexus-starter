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
 * Utility to display a CaptainHook config
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhook-git/captainhook
 * @since   Class available since Release 5.29.1
 */
abstract class Util
{
    /**
     * Return yes or no emoji
     *
     * @param bool $bool
     * @return string
     */
    public static function yesOrNo(bool $bool): string
    {
        return $bool ? '✅' : '⛔️';
    }

    /**
     * Make sure we do not output the line breaks
     *
     * @param  string $value
     * @return string
     */
    public static function escapeLineBreaks(string $value): string
    {
        return str_replace(
            ["\r\n", "\r", "\n"],
            "\\n",
            $value
        );
    }
}
