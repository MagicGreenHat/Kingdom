<?php

namespace FOS\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

/** {@inheritDoc} */
class RegistrationType extends RegistrationFormType
{

    /** {@inheritDoc} */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'email',
            'email',
            [
                'label'              => 'form.email',
                'translation_domain' => 'FOSUserBundle',
            ]
        );

        $builder->add(
            'username',
            null,
            [
                'label'              => 'form.username',
                'translation_domain' => 'FOSUserBundle',
            ]
        );

        $builder->add(
            'plainPassword',
            'repeated',
            [
                'type'            => 'password',
                'options'         => ['translation_domain' => 'FOSUserBundle'],
                'first_options'   => ['label' => 'form.password'],
                'second_options'  => ['label' => 'form.password_confirmation'],
                'invalid_message' => 'fos_user.password.mismatch',
            ]
        );
    }
}
