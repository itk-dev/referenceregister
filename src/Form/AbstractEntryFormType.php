<?php

namespace App\Form;

use App\Entity\Department;
use App\Entity\Entry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatableInterface;

use function Symfony\Component\Translation\t;

abstract class AbstractEntryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $label = $this->getIdLabel();
        $builder
            ->add('hash', null, [
                'label' => false,
                'attr' => [
                    'autocomplete' => 'off',
                    'placeholder' => $label,
                ],
                'help' => $this->getIdHelp(),
            ])
            ->add('department', EntityType::class, [
                'class' => Department::class,
                'label' => t('Department'),
                'placeholder' => t('Choose department'),
            ])
            ->add('submit', SubmitType::class, [
                'label' => $this->getSubmitLabel(),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Entry::class,
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
