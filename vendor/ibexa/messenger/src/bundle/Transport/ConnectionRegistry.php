<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Messenger\Transport;

use Doctrine\Persistence\ConnectionRegistry as BaseConnectionRegistry;
use Ibexa\Contracts\Core\Container\ApiLoader\RepositoryConfigurationProviderInterface;

final class ConnectionRegistry implements BaseConnectionRegistry
{
    public const DEFAULT_IBEXA_CONNECTION = 'ibexa.current';

    private BaseConnectionRegistry $registry;

    private RepositoryConfigurationProviderInterface $repositoryConfigurationProvider;

    public function __construct(
        BaseConnectionRegistry $registry,
        RepositoryConfigurationProviderInterface $repositoryConfigurationProvider
    ) {
        $this->registry = $registry;
        $this->repositoryConfigurationProvider = $repositoryConfigurationProvider;
    }

    public function getDefaultConnectionName(): string
    {
        return $this->registry->getDefaultConnectionName();
    }

    public function getConnection(?string $name = null): object
    {
        if ($name === self::DEFAULT_IBEXA_CONNECTION) {
            $name = $this->repositoryConfigurationProvider->getStorageConnectionName();
        }

        return $this->registry->getConnection($name);
    }

    public function getConnections(): array
    {
        return $this->registry->getConnections();
    }

    public function getConnectionNames(): array
    {
        return $this->registry->getConnectionNames();
    }
}
