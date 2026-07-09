<?php

namespace App\Form;

use App\Entity\Department;
use App\Model\EntryFormDto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatableInterface;

use function Symfony\Component\Translation\t;

abstract class AbstractEntryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('identifier', TextType::class, [
                'label' => $this->getIdentifierLabel(),
                'attr' => [
                    'autocomplete' => 'off',
                ],
                'help' => $this->getIdentifierHelp(),
            ])
            ->add('department', EntityType::class, [
                'class' => Department::class,
                'label' => $this->getDepartmentLabel(),
                'placeholder' => '',
                'help' => $this->getDepartmentHelp(),
            ])
            ->add('submit', SubmitType::class, [
                'label' => $this->getSubmitLabel(),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EntryFormDto::class,
        ]);
    }

    protected function getIdentifierLabel(): TranslatableInterface
    {
        return t('Identifier');
    }

    protected function getDepartmentLabel(): TranslatableInterface
    {
        return t('Department');
    }

    protected function getIdentifierHelp(): ?TranslatableInterface
    {
        return null;
    }

    protected function getDepartmentHelp(): ?TranslatableInterface
    {
        return null;
    }

    abstract protected function getSubmitLabel(): TranslatableInterface;
}
