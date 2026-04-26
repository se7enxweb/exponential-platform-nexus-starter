<?php

declare(strict_types=1);

namespace Netgen\TagsBundle\Core\REST\Output\ValueObjectVisitor;

use FOS\HttpCache\ResponseTagger;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class CachedValue extends ValueObjectVisitor
{
    public function __construct(
        private RequestStack $requestStack,
        private ConfigResolverInterface $configResolver,
        private ResponseTagger $responseTagger,
    ) {}

    public function visit(Visitor $visitor, Generator $generator, mixed $data): void
    {
        $visitor->visitValueObject($data->value);

        if ($this->getParameter('tag_view.cache') !== true) {
            return;
        }

        $response = $visitor->getResponse();
        $response->setPublic();
        $response->setVary('Accept');

        if ($this->getParameter('tag_view.ttl_cache') === true) {
            $response->setSharedMaxAge($this->getParameter('tag_view.default_ttl'));

            $request = $this->requestStack->getCurrentRequest();
            if ($request instanceof Request && $request->headers->has('X-User-Hash')) {
                $response->setVary('X-User-Hash', false);
            }
        }

        if (isset($data->cacheTags['tagId'])) {
            $this->responseTagger->addTags(['ngtags-tag-' . $data->cacheTags['tagId']]);
        }

        if (isset($data->cacheTags['tagKeyword'])) {
            $this->responseTagger->addTags(['ngtags-tag-keyword-' . $data->cacheTags['tagKeyword']]);
        }
    }

    /**
     * Returns the parameter value from config resolver.
     */
    private function getParameter(string $parameterName): mixed
    {
        if ($this->configResolver->hasParameter($parameterName, 'netgen_tags')) {
            return $this->configResolver->getParameter($parameterName, 'netgen_tags');
        }

        return null;
    }
}
