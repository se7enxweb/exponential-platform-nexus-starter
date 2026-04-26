<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaAdminUIExtraBundle\DependencyInjection\Security\PolicyProvider;

use Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\YamlPolicyProvider;

final class QueuesPolicyProvider extends YamlPolicyProvider
{
    public function getFiles(): array
    {
        return [
            __DIR__ . '/../../../Resources/config/policies.yaml',
        ];
    }
}
