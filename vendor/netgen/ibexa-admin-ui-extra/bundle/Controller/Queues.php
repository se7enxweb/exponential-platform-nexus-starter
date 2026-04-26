<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaAdminUIExtraBundle\Controller;

use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Transport\Receiver\MessageCountAwareInterface;

use function array_keys;
use function strcmp;
use function usort;

final class Queues extends Controller
{
    public function __construct(
        public readonly ServiceLocator $transportLocator,
        private readonly bool $queuesEnabled,
        private readonly array $allowedTransports,
        private readonly PermissionResolver $permissionResolver,
    ) {}

    public function __invoke(): Response
    {
        if (!$this->queuesEnabled) {
            throw new NotFoundHttpException('Queues feature is disabled.');
        }

        if (!$this->permissionResolver->hasAccess('queues', 'read')) {
            throw new AccessDeniedHttpException('You do not have permission to access queues.');
        }

        $transportNames = !empty($this->allowedTransports)
            ? $this->allowedTransports
            : $this->getAllTransportAliases();

        $queueData = [];

        foreach ($transportNames as $transportName) {
            if (!$this->transportLocator->has($transportName)) {
                continue;
            }

            $transport = $this->transportLocator->get($transportName);

            if (!$transport instanceof MessageCountAwareInterface) {
                continue;
            }

            $queueData[] = [
                'name' => $transportName,
                'count' => $transport->getMessageCount(),
            ];
        }

        usort($queueData, static fn (array $first, array $second) => strcmp($first['name'], $second['name']));

        return $this->render(
            '@NetgenIbexaAdminUIExtra/queues/list.html.twig',
            [
                'translation_domain' => 'netgen_admin_ui_extra',
                'queue_data' => $queueData,
            ],
        );
    }

    private function getAllTransportAliases(): iterable
    {
        $names = array_keys($this->transportLocator->getProvidedServices());

        foreach ($names as $name) {
            if (!str_starts_with($name, 'messenger.transport.')) {
                yield $name;
            }
        }
    }
}
