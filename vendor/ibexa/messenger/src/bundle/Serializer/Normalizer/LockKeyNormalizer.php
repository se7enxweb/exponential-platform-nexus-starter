<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Messenger\Serializer\Normalizer;

use Closure;
use ReflectionClass;
use Symfony\Component\Lock\Key;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Backport of Symfony normalizer for Lock keys.
 */
final class LockKeyNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function getSupportedTypes(?string $format): array
    {
        return [
            Key::class => true,
        ];
    }

    /**
     * @return array<mixed>
     */
    public function normalize($data, ?string $format = null, array $context = []): array
    {
        assert($data instanceof Key);

        return $data->__serialize();
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Key;
    }

    /**
     * @throws \ReflectionException
     */
    public function denormalize($data, string $type, ?string $format = null, array $context = []): Key
    {
        $key = (new ReflectionClass(Key::class))->newInstanceWithoutConstructor();
        $setter = Closure::bind(
            function (string $field) use ($data): void {
                $this->$field = $data[$field];
            },
            $key,
            Key::class,
        );
        foreach (['resource', 'expiringTime', 'state'] as $serializedField) {
            $setter($serializedField);
        }

        return $key;
    }

    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === Key::class;
    }
}
