<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaScheduledVisibilityBundle\Core\Enum;

enum VisibilityUpdateResult: string
{
    case Hidden = 'hidden';

    case Revealed = 'revealed';

    case NoChange = 'no_change';
}
