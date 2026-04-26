<?php

declare(strict_types=1);

namespace Netgen\TagsBundle\ValueResolver;

use Netgen\TagsBundle\API\Repository\TagsService;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

use function is_a;

final class TagValueResolver implements ValueResolverInterface
{
    public function __construct(
        private TagsService $tagsService,
    ) {}

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() === null || !is_a($argument->getType(), Tag::class, true)) {
            return [];
        }

        $tagId = null;

        if ($argument->getName() === 'tag' && $request->attributes->has('tagId')) {
            $tagId = $request->attributes->getInt('tagId');
        } elseif ($argument->getName() === 'parentTag' && $request->attributes->has('parentId')) {
            $tagId = $request->attributes->getInt('parentId');
        }

        if ($tagId === null || $tagId <= 0) {
            return [];
        }

        yield $this->tagsService->loadTag($tagId);
    }
}
