<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Ibexa\PolicyProvider;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ConfigBuilderInterface;
use Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\PolicyProviderInterface;
use Netgen\InformationCollection\API\Permissions;

class InformationCollectionPolicyProvider implements PolicyProviderInterface
{
    public function addPolicies(ConfigBuilderInterface $configBuilder): array
    {
        $configBuilder->addConfig([
            Permissions::NAME => [
                Permissions::POLICY_READ => [],
                Permissions::POLICY_DELETE => [],
                Permissions::POLICY_ANONYMIZE => [],
                Permissions::POLICY_EXPORT => [],
            ],
        ]);

        return [];
    }
}
