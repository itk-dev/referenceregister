<?php

namespace App\Form;

use Symfony\Contracts\Translation\TranslatableInterface;

use function Symfony\Component\Translation\t;

class EntryLookUpFormType extends AbstractIdFormType
{
    protected function getSubmitLabel(): TranslatableInterface
    {
        return t('Look up entry');
    }
}
