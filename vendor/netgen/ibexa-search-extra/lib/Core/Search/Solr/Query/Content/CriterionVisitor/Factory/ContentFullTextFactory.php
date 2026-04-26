<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\Core\Search\Solr\Query\Content\CriterionVisitor\Factory;

use Ibexa\Contracts\Core\Persistence\Content\Type\Handler;
use Netgen\IbexaSearchExtra\Core\Search\Solr\Query\Content\CriterionVisitor\FullText;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use QueryTranslator\Languages\Galach\Generators\ExtendedDisMax;
use QueryTranslator\Languages\Galach\Parser;
use QueryTranslator\Languages\Galach\Tokenizer;

final class ContentFullTextFactory
{
    public function __construct(
        private readonly Tokenizer $tokenizer,
        private readonly Parser $parser,
        private readonly ExtendedDisMax $generator,
        private readonly Handler $contentTypeHandler,
    ) {}

    public function createCriterionVisitor(): CriterionVisitor
    {
        return new FullText(
            $this->tokenizer,
            $this->parser,
            $this->generator,
            $this->contentTypeHandler,
        );
    }
}
