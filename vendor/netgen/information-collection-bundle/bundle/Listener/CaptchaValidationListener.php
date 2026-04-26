<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class CaptchaValidationListener implements EventSubscriberInterface
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::POST_SUBMIT => 'onPostSubmit',
        ];
    }

    public function onPostSubmit(FormEvent $event): void
    {
        $captchaValue = $event->getForm()
            ->getConfig()
            ->getOption('captcha_value');

        $request = Request::createFromGlobals();

        if (!$captchaValue->isValid($request)) {
            $error = new FormError(
                $this->translator->trans('netgen_information_collection.captcha'),
            );

            $event->getForm()->addError($error);
        }
    }
}
