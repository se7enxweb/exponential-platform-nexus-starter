<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Messenger\Serializer\Normalizer;

use Closure;
use Ibexa\Bundle\Messenger\Stamp\DeduplicateStamp;
use ReflectionClass;
use Symfony\Component\Lock\Key;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Backport of Symfony normalizer for DeduplicateStamp.
 *
 * @phpstan-type TData array{
 *     key: mixed,
 *     ttl?: float|null,
 *     only_deduplicate_in_queue?: bool|null,
 * }
 */
final class DeduplicateStampNormalizer implements NormalizerInterface, DenormalizerInterface, NormalizerAwareInterface, DenormalizerAwareInterface
{
    use NormalizerAwareTrait;

    use DenormalizerAwareTrait;

    public function getSupportedTypes(?string $format): array
    {
        return [
            DeduplicateStamp::class => true,
        ];
    }

    /**
     * @phpstan-param TData $data
     */
    public function denormalize($data, string $type, ?string $format = null, array $context = []): DeduplicateStamp
    {
        $stamp = (new ReflectionClass(DeduplicateStamp::class))->newInstanceWithoutConstructor();

        $key = $this->denormalizer->denormalize($data['key'], Key::class, $format, $context);

        Closure::bind(function () use ($data, $key): void {
            $this->key = $key;
            $this->ttl = $data['ttl'] ?? 300.0;
            $this->onlyDeduplicateInQueue = $data['only_deduplicate_in_queue'] ?? false;
        }, $stamp, DeduplicateStamp::class)();

        return $stamp;
    }

    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === DeduplicateStamp::class;
    }

    /**
     * @phpstan-return TData
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        assert($object instanceof DeduplicateStamp);

        return [
            'key' => $this->normalizer->normalize($object->getKey(), $format, $context),
            'ttl' => $object->getTtl(),
            'only_deduplicate_in_queue' => $object->onlyDeduplicateInQueue(),
        ];
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof DeduplicateStamp;
    }
}
