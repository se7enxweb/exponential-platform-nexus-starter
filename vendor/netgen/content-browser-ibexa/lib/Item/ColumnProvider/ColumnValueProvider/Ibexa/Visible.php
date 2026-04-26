<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Ibexa\Item\ColumnProvider\ColumnValueProvider\Ibexa;

use Netgen\ContentBrowser\Ibexa\Item\Ibexa\IbexaInterface;
use Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProviderInterface;
use Netgen\ContentBrowser\Item\ItemInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use function sprintf;

final class Visible implements ColumnValueProviderInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {}

    public function getValue(ItemInterface $item): ?string
    {
        if (!$item instanceof IbexaInterface) {
            return null;
        }

        return $this->translator->trans(
            sprintf(
                'columns.ibexa.visible.%s',
                $item->location->invisible ? 'no' : 'yes',
            ),
            [],
            'ngcb',
        );
    }
}
