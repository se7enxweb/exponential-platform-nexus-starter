<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Messenger\Stamp;

use Symfony\Component\Lock\Key;
use Symfony\Component\Messenger\Exception\LogicException;
use Symfony\Component\Messenger\Stamp\StampInterface;

/**
 * (c) Fabien Potencier <fabien@symfony.com>.
 *
 * Original code: https://github.com/symfony/symfony/blob/7.3/src/Symfony/Component/Messenger/Stamp/DeduplicateStamp.php
 */
final class DeduplicateStamp implements StampInterface
{
    private Key $key;

    private ?float $ttl;

    private bool $onlyDeduplicateInQueue;

    public function __construct(
        string $key,
        ?float $ttl = 300.0,
        bool $onlyDeduplicateInQueue = false
    ) {
        if (!class_exists(Key::class)) {
            throw new LogicException(sprintf(
                'You cannot use the "%s" as the Lock component is not installed. Try running "composer require symfony/lock".',
                self::class,
            ));
        }

        $this->key = new Key($key);
        $this->ttl = $ttl;
        $this->onlyDeduplicateInQueue = $onlyDeduplicateInQueue;
    }

    public function onlyDeduplicateInQueue(): bool
    {
        return $this->onlyDeduplicateInQueue;
    }

    public function getKey(): Key
    {
        return $this->key;
    }

    public function getTtl(): ?float
    {
        return $this->ttl;
    }
}
