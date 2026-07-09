<?php

namespace App\Form;

use Symfony\Contracts\Translation\TranslatableInterface;

use function Symfony\Component\Translation\t;

class EntryAddFormType extends AbstractEntryFormType
{
    protected function getSubmitLabel(): TranslatableInterface
    {
        return t('Add entry');
    }

    protected function getIdentifierHelp(): ?TranslatableInterface
    {
        return t('Enter ID to add');
    }
}
