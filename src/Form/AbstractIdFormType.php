<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatableInterface;

use function Symfony\Component\Translation\t;

abstract class AbstractIdFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $label = $this->getIdLabel();
        $builder
            ->add('id', null, [
                'label' => false,
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'off',
                    'placeholder' => $label,
                ],
                'help' => $this->getIdHelp(),
            ])
            ->add('submit', SubmitType::class, [
                'label' => $this->getSubmitLabel(),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }

    protected function getIdLabel(): TranslatableInterface
    {
        return t('ID');
    }

    protected function getIdHelp(): ?TranslatableInterface
    {
        return null;
    }

    abstract protected function getSubmitLabel(): TranslatableInterface;
}
