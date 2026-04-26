<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Messenger;

use Symfony\Component\Config\Loader\LoaderInterface;

final class RedisTestKernel extends AbstractTestKernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        parent::registerContainerConfiguration($loader);

        $loader->load(__DIR__ . '/Resources/redis/ibexa_messenger.yaml');
        $loader->load(__DIR__ . '/Resources/redis/framework.yaml');
    }
}
