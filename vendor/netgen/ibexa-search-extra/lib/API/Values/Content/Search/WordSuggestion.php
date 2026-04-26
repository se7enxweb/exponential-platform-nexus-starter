<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\API\Values\Content\Search;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class WordSuggestion extends ValueObject
{
    public string $originalWord;
    public string $suggestedWord;
    public int $frequency;
}
