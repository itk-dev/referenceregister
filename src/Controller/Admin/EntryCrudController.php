<?php

namespace App\Controller\Admin;

use App\Entity\Entry;
use App\Entity\Role;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

use function Symfony\Component\Translation\t;

class EntryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Entry::class;
    }

    #[\Override]
    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityPermission(Role::Administrator->value)
            ->setEntityLabelInSingular(t('Entry'))
            ->setEntityLabelInPlural(t('Entries'));
    }

    #[\Override]
    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->disable(Action::NEW, Action::EDIT, Action::DELETE);
    }

    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('hash', t('Hash', domain: 'entry'))
            ->setSortable(false);
        yield AssociationField::new('department', t('Department', domain: 'entry'));
        yield DateTimeField::new('createdAt', t('Created at'));
        yield DateTimeField::new('updatedAt', t('Updated at'));
        yield DateTimeField::new('expiredAt', t('Expired at'));
    }
}
