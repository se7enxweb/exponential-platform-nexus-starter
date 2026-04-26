<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\ValueResolver;

use Netgen\InformationCollection\API\Service\InformationCollection;
use Netgen\InformationCollection\API\Value\Collection;
use Netgen\InformationCollection\API\Value\Filter\CollectionId;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

use function is_a;

final class CollectionValueResolver implements ValueResolverInterface
{
    private InformationCollection $informationCollection;

    public function __construct(InformationCollection $informationCollection)
    {
        $this->informationCollection = $informationCollection;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!is_a($argument->getType() ?? '', Collection::class, true)) {
            return [];
        }

        if ($argument->getName() !== 'collection') {
            return [];
        }

        if (!$request->attributes->has('collectionId')) {
            return [];
        }

        $collectionId = $request->attributes->getInt('collectionId');

        yield $this->informationCollection->getCollection(new CollectionId($collectionId));
    }
}
