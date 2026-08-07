<?php

namespace App\Form;

use App\Entity\Department\LookupSlot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\Translation\t;

class LookupSlotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startsAt', ChoiceType::class, [
                'label' => t('Starts at', domain: 'department'),
                'choices' => [
                    'At midnight' => LookupSlot::STARTS_AT_MIDNIGHT,
                    '24 hours ago' => LookupSlot::STARTS_AT_24_HOURS_AGO,
                ],
                'choice_translation_domain' => 'department',
            ])
            ->add('maxLookups', IntegerType::class, [
                'label' => t('Max lookups', domain: 'department'),
                'attr' => [
                    'min' => LookupSlot::LOOKUPS_MIN,
                    'max' => LookupSlot::LOOKUPS_MAX,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LookupSlot::class,
        ]);
    }
}
