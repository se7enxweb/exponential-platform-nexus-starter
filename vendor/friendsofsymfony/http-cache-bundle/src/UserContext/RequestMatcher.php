<?php

/*
 * This file is part of the FOSHttpCacheBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\HttpCacheBundle\UserContext;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

final class RequestMatcher implements RequestMatcherInterface
{
    public function __construct(
        private ?string $accept = 'application/vnd.fos.user-context-hash',
        private ?string $method = null,
    ) {
    }

    public function matches(Request $request): bool
    {
        if (null !== $this->accept && $this->accept != $request->headers->get('accept', null)) {
            return false;
        }

        if (null !== $this->method && $this->method != $request->getMethod()) {
            return false;
        }

        return true;
    }
}
