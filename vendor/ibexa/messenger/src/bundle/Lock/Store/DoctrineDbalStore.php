<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Messenger\Lock\Store;

use Closure;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Symfony\Component\Lock\Exception\LockConflictedException;
use Symfony\Component\Lock\Key;
use Symfony\Component\Lock\Store\DoctrineDbalStore as SymfonyDoctrineDbalStore;

final class DoctrineDbalStore extends SymfonyDoctrineDbalStore
{
    private Connection $connection;

    public function __construct($connOrUrl, array $options = [], float $gcProbability = 0.01, int $initialTtl = 300)
    {
        parent::__construct($connOrUrl, $options, $gcProbability, $initialTtl);

        $this->connection = Closure::bind(function (): Connection {
            return $this->conn;
        }, $this, SymfonyDoctrineDbalStore::class)();
    }

    public function save(Key $key): void
    {
        if ($this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            $this->handlePostgresLock($key);

            return;
        }

        parent::save($key);
    }

    /**
     * This method overwrites the way lock works against PostgreSQL database to ensure it does not cause application
     * failure when in transaction.
     */
    private function handlePostgresLock(Key $key): void
    {
        $extractedFeatures = Closure::bind(function (): array {
            return [
                $this->table,
                $this->idCol,
                $this->tokenCol,
                $this->expirationCol,
                $this->initialTtl,
                Closure::fromCallable([$this, 'getHashedKey']),
                Closure::fromCallable([$this, 'getUniqueToken']),
            ];
        }, $this, SymfonyDoctrineDbalStore::class);

        [
            $table,
            $idCol,
            $tokenCol,
            $expirationCol,
            $initialTtl,
            $getHashedKey,
            $getUniqueToken,
        ] = $extractedFeatures();

        $sql = "SELECT $idCol, $tokenCol, $expirationCol FROM $table WHERE $idCol = ?";

        $hashKey = $getHashedKey($key);

        $result = $this->connection->executeQuery($sql, [
            $hashKey,
        ], [
            ParameterType::STRING,
        ]);

        $result = $result->fetchAssociative();
        if ($result === false) {
            parent::save($key);

            return;
        }

        $uniqueToken = $getUniqueToken($key);
        if ($uniqueToken === $result[$tokenCol]) {
            $this->putOffExpiration($key, $initialTtl);

            return;
        }

        throw new LockConflictedException();
    }
}
