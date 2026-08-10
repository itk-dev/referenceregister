<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatableInterface;

use function Symfony\Component\Translation\t;

class EntryLookUpFormType extends AbstractEntryFormType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder->remove('department');
    }

    protected function getSubmitLabel(): TranslatableInterface
    {
        return t('Look up entry');
    }
}
