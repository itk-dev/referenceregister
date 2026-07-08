<?php

namespace App\Form;

use Symfony\Contracts\Translation\TranslatableInterface;

use function Symfony\Component\Translation\t;

class EntryRemoveFormType extends AbstractEntryFormType
{
    protected function getSubmitLabel(): TranslatableInterface
    {
        return t('Remove entry');
    }
}
