<?php

/*
 * This file is part of the FOSHttpCache package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\HttpCache\SymfonyCache;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Contracts\EventDispatcher\Event as BaseEvent;

/**
 * Event raised by the HttpCache kernel.
 *
 * @author David Buchmann <mail@davidbu.ch>
 */
class CacheEvent extends BaseEvent
{
    /**
     * Make sure your $kernel implements the CacheInvalidation interface.
     */
    public function __construct(
        /**
         * The kernel raising this event.
         */
        private readonly CacheInvalidation $kernel,
        private readonly Request $request,
        private ?Response $response = null,
        private readonly int $requestType = HttpKernelInterface::MAIN_REQUEST,
    ) {
    }

    /**
     * Get the cache kernel that raised this event.
     */
    public function getKernel(): CacheInvalidation
    {
        return $this->kernel;
    }

    /**
     * Get the request that is being processed.
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * One of the constants HttpKernelInterface::MAIN_REQUEST or SUB_REQUEST.
     */
    public function getRequestType(): int
    {
        return $this->requestType;
    }

    /**
     * Events that occur after the response is created provide the default response.
     * Event listeners can also set the response to make it available here.
     */
    public function getResponse(): ?Response
    {
        return $this->response;
    }

    /**
     * Sets a response to use instead of continuing to handle this request.
     *
     * Setting a response stops propagation of the event to further event handlers.
     */
    public function setResponse(Response $response): void
    {
        $this->response = $response;

        $this->stopPropagation();
    }
}
