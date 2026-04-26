<?php

/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Runner\Bootstrap;

use CaptainHook\App\Config;
use CaptainHook\App\Console\Runtime\Resolver;
use RuntimeException;
use Throwable;

/**
 * Bootstrap Util
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhook-git/captainhook
 * @since   Class available since Release 5.23.3
 */
class Util
{
    /**
     * Check if bootstrapping is required and do it if needed
     *
     * @param  \CaptainHook\App\Config $config
     * @param  \CaptainHook\App\Console\Runtime\Resolver $resolver
     * @return void
     * @throws \RuntimeException
     */
    public static function handleBootstrap(Config $config, Resolver $resolver): void
    {
        // we only have to care about bootstrapping PHAR builds because for
        // Composer installations the bootstrapping is already done in the bin script
        if (self::isBootstrapRequired($config, $resolver)) {
            // check the custom and default autoloader
            $bootstrapFile = self::validateBootstrapPath($resolver->isPharRelease(), $config);
            // since the phar has its own autoloader, we don't need to do anything
            // if the bootstrap file is not actively set
            if (empty($bootstrapFile)) {
                return;
            }
            // the bootstrap file exists, so let's load it
            try {
                require $bootstrapFile;
            } catch (Throwable $t) {
                throw new RuntimeException(
                    'Loading bootstrap file failed: ' . $bootstrapFile . PHP_EOL .
                    $t->getMessage() . PHP_EOL
                );
            }
        }
    }

    /**
     * Checks if we have to bootstrap the application
     *
     * If we run in composer mode and the bootstrap file is `vendor/autoload.php`, we don't need to do anything
     * In PHAR mode we need to do bootstrap anyway.
     *
     * @param  \CaptainHook\App\Config $config
     * @param  \CaptainHook\App\Console\Runtime\Resolver $resolver
     * @return bool
     */
    public static function isBootstrapRequired(Config $config, Resolver $resolver): bool
    {
        return $resolver->isPharRelease() || $config->getBootstrap() !== 'vendor/autoload.php';
    }

    /**
     * Return the bootstrap file to load (can be empty)
     *
     * @param  bool                    $isPhar
     * @param  \CaptainHook\App\Config $config
     * @return string
     */
    public static function validateBootstrapPath(bool $isPhar, Config $config): string
    {
        $bootstrapFile = dirname($config->getPath()) . '/' . $config->getBootstrap();
        if (!file_exists($bootstrapFile)) {
            // since the phar has its own autoloader we don't need to do anything
            // if the bootstrap file is not actively set
            if ($isPhar && empty($config->getBootstrap(''))) {
                return '';
            }
            throw new RuntimeException('bootstrap file not found');
        }
        return $bootstrapFile;
    }

    /**
     * Returns the bootstrap command option (can be empty)
     *
     * @param bool                    $isPhar
     * @param \CaptainHook\App\Config $config
     * @return string
     */
    public static function bootstrapCmdOption(bool $isPhar, Config $config): string
    {
        // nothing to load => no option
        if ($isPhar && empty($config->getBootstrap(''))) {
            return '';
        }
        return ' --bootstrap=' . $config->getBootstrap();
    }
}
