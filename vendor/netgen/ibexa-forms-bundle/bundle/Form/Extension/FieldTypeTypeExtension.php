<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaFormsBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FieldTypeTypeExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        // Returning 'form' extends all form types
        return [FormType::class];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined(
            [
                'ibexa_forms',
            ]
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $ibexaFormsVars = [];

        if (isset($options['ibexa_forms']['fielddefinition'])) {
            $ibexaFormsVars['fielddefinition'] = $options['ibexa_forms']['fielddefinition'];
        }

        if (isset($options['ibexa_forms']['language_code'])) {
            $ibexaFormsVars['language_code'] = $options['ibexa_forms']['language_code'];
        }

        if (isset($options['ibexa_forms']['content'])) {
            $ibexaFormsVars['content'] = $options['ibexa_forms']['content'];
        }

        if (isset($options['ibexa_forms']['description'])) {
            $ibexaFormsVars['description'] = $options['ibexa_forms']['description'];
        }

        $view->vars['ibexa_forms'] = $ibexaFormsVars;
    }
}
