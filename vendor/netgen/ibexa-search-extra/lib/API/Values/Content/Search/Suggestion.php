<?php

declare(strict_types=1);

namespace Netgen\IbexaSearchExtra\API\Values\Content\Search;

use InvalidArgumentException;

use function array_key_exists;
use function array_keys;
use function array_map;
use function array_values;
use function str_replace;

class Suggestion
{
    /**
     * @var \Netgen\IbexaSearchExtra\API\Values\Content\Search\WordSuggestion[][]
     */
    private array $suggestionsByOriginalWords = [];

    /**
     * @param \Netgen\IbexaSearchExtra\API\Values\Content\Search\WordSuggestion[] $wordSuggestions
     */
    public function __construct(array $wordSuggestions = [])
    {
        foreach ($wordSuggestions as $suggestion) {
            if (!array_key_exists($suggestion->originalWord, $this->suggestionsByOriginalWords)) {
                $this->suggestionsByOriginalWords[$suggestion->originalWord] = [];
            }

            $this->suggestionsByOriginalWords[$suggestion->originalWord][] = $suggestion;
        }
    }

    /**
     * @return bool
     */
    public function hasSuggestions(): bool
    {
        return !empty($this->suggestionsByOriginalWords);
    }

    /**
     * @return \Netgen\IbexaSearchExtra\API\Values\Content\Search\WordSuggestion[][]
     */
    public function getSuggestions(): array
    {
        return array_values($this->suggestionsByOriginalWords);
    }

    /**
     * @return string[]
     */
    public function getOriginalWords(): array
    {
        return array_map('strval', array_keys($this->suggestionsByOriginalWords));
    }

    /**
     * @param string $originalWord
     *
     * @throws \InvalidArgumentException
     *
     * @return \Netgen\IbexaSearchExtra\API\Values\Content\Search\WordSuggestion[]
     */
    public function getSuggestionsByOriginalWord(string $originalWord): array
    {
        if (!array_key_exists($originalWord, $this->suggestionsByOriginalWords)) {
            throw new InvalidArgumentException('No suggestions found for the given word');
        }

        return $this->suggestionsByOriginalWords[$originalWord];
    }

    /**
     * Get suggested search text based on returned spell check suggestions.
     */
    public function getSuggestedSearchText(string $originalSearchText): ?string
    {
        $originalWords = $this->getOriginalWords();
        $suggestedWords = [];

        foreach ($originalWords as $originalWord) {
            $suggestedWords[] = $this->getSuggestionsByOriginalWord($originalWord)[0]->suggestedWord;
        }

        $suggestedSearchText = str_replace($originalWords, $suggestedWords, $originalSearchText);

        if ($originalSearchText === $suggestedSearchText) {
            return null;
        }

        return $suggestedSearchText;
    }
}
