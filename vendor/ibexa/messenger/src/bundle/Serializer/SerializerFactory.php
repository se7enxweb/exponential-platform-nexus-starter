<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Messenger\Serializer;

use function Ibexa\PolyfillPhp82\iterator_to_array;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;

final class SerializerFactory
{
    /** @var iterable<\Symfony\Component\Serializer\Normalizer\NormalizerInterface|\Symfony\Component\Serializer\Normalizer\DenormalizerInterface> */
    private iterable $normalizers;

    /**
     * @param iterable<\Symfony\Component\Serializer\Normalizer\NormalizerInterface|\Symfony\Component\Serializer\Normalizer\DenormalizerInterface> $normalizers
     */
    public function __construct(
        iterable $normalizers
    ) {
        $this->normalizers = $normalizers;
    }

    public function create(): Serializer
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];

        $serializer = new SymfonySerializer(
            iterator_to_array($this->normalizers),
            $encoders,
        );

        return new Serializer($serializer);
    }
}
