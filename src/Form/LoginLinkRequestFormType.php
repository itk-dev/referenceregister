<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

use function Symfony\Component\Translation\t;

final class LoginLinkRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => t('Email address'),
                'help' => t('Enter your email address to receive a login link'),
                'attr' => [
                    'autofocus' => true,
                ],
                'constraints' => [
                    new Email(),
                    // Email considers empty values valid, so NotBlank is needed
                    // to actually make the field required (cf.
                    // https://symfony.com/doc/current/reference/constraints/Email.html).
                    new NotBlank(),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => t('Send login link'),
            ])
        ;
    }
}
