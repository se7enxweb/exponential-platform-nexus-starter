<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaScheduledVisibilityBundle\Core;

use OutOfBoundsException;

use function sprintf;

final class Registry
{
    /**
     * @var \Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\VisibilityHandler[]
     */
    private array $handlerMap = [];

    /**
     * @param \Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\VisibilityHandler[] $handlerMap
     */
    public function __construct(array $handlerMap = [])
    {
        foreach ($handlerMap as $identifier => $handler) {
            $this->register($identifier, $handler);
        }
    }

    public function register(string $identifier, VisibilityHandler $handler): void
    {
        $this->handlerMap[$identifier] = $handler;
    }

    /**
     * @throws OutOfBoundsException
     */
    public function get(?string $identifier): VisibilityHandler
    {
        return $this->handlerMap[$identifier] ?? throw new OutOfBoundsException(
            sprintf(
                "No handler is registered for identifier '%s'",
                $identifier,
            ),
        );
    }
}
