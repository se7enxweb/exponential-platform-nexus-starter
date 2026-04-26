<?php

/*
 * This file is part of the FOSHttpCacheBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\HttpCacheBundle;

use FOS\HttpCache\ProxyClient\Invalidation\TagCapable;

class UserContextInvalidator
{
    public const USER_CONTEXT_TAG_PREFIX = 'fos_http_cache_hashlookup-';

    public function __construct(
        private readonly TagCapable $tagger,
    ) {
    }

    /**
     * Invalidate the user context hash.
     */
    public function invalidateContext(string $sessionId): void
    {
        $this->tagger->invalidateTags([static::buildTag($sessionId)]);
    }

    public static function buildTag(string $hash): string
    {
        return static::USER_CONTEXT_TAG_PREFIX.$hash;
    }
}
