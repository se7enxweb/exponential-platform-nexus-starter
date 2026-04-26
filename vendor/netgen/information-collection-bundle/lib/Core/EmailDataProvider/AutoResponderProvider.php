<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Core\EmailDataProvider;

use Netgen\InformationCollection\API\Action\EmailDataProviderInterface;
use Netgen\InformationCollection\API\Value\Event\InformationCollected;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Header\UnstructuredHeader;

class AutoResponderProvider implements EmailDataProviderInterface
{
    public function provide(InformationCollected $value): Email
    {
        $email = new Email();

        $headers = $email->getHeaders();
        $headers->add(new UnstructuredHeader('Content-Type', ''));

        return $email;
    }
}
