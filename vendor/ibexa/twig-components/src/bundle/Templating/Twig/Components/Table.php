<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\TwigComponents\Templating\Twig\Components;

use Ibexa\Bundle\TwigComponents\Templating\Twig\Components\Table\Column;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent(
    name: 'ibexa.Table',
    template: '@ibexadesign/twig_components/table.html.twig',
)]
final class Table
{
    /** @var iterable<object> */
    #[ExposeInTemplate]
    private iterable $data = [];

    /** @var class-string|null */
    private ?string $dataType = null;

    /** @var array<string, mixed> */
    public array $parameters = [];

    /** @var array<string, Column> */
    private array $columns = [];

    /** @var array<string, Column>|null */
    private ?array $orderedColumns = null;

    /**
     * @param iterable<object> $data
     */
    public function mount(iterable $data = []): void
    {
        if ($data !== []) {
            $this->data = $data;
        }
    }

    /**
     * @return iterable<object>
     */
    public function getData(): iterable
    {
        return $this->data;
    }

    /**
     * @return class-string|null
     */
    public function getDataType(): ?string
    {
        return $this->dataType ??= $this->resolveDataType();
    }

    /**
     * @return array<string, Column>
     */
    #[ExposeInTemplate('columns')]
    public function getColumns(): array
    {
        return $this->orderedColumns ??= $this->orderColumns();
    }

    /**
     * @return array<string, Column>
     */
    private function orderColumns(): array
    {
        $columns = $this->columns;

        uasort($columns, static fn (Column $a, Column $b): int => $b->priority <=> $a->priority);

        return $columns;
    }

    /**
     * @phpstan-param callable(Column): string $label
     * @phpstan-param callable(mixed, Column): string $renderer
     */
    public function addColumn(string $identifier, callable $label, callable $renderer, int $priority = 0): self
    {
        $this->orderedColumns = null;
        $this->columns[$identifier] = new Column(
            $identifier,
            $label(...),
            $renderer(...),
            $priority,
        );

        return $this;
    }

    public function removeColumn(string $identifier): self
    {
        $this->orderedColumns = null;
        unset($this->columns[$identifier]);

        return $this;
    }

    /**
     * @phpstan-return class-string|null
     */
    private function resolveDataType(): ?string
    {
        $firstItem = null;
        foreach ($this->data as $item) {
            $firstItem = $item;
            break;
        }

        if (!is_object($firstItem)) {
            return null;
        }

        $candidates = array_merge(
            [get_class($firstItem)],
            class_parents($firstItem),
            class_implements($firstItem)
        );

        foreach ($this->data as $item) {
            $candidates = array_filter($candidates, static fn ($candidate): bool => $item instanceof $candidate);
            if (empty($candidates)) {
                return null;
            }
        }

        /** @var class-string|null $inferredType */
        $inferredType = reset($candidates);

        return $inferredType;
    }

    public function renderCell(Column $column, mixed $item): string
    {
        return ($column->renderer)($item, $column);
    }

    public function renderLabel(Column $column): string
    {
        return ($column->label)($column);
    }
}
