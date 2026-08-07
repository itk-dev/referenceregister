<?php

namespace App\Controller\Admin;

use App\Admin\Field\LookupSlotField;
use App\Entity\Department;
use App\Entity\Role;
use App\Form\ContactPersonType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

use function Symfony\Component\Translation\t;

class DepartmentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Department::class;
    }

    #[\Override]
    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityPermission(Role::DepartmentEditor->value)
            ->setEntityLabelInSingular(t('Department'))
            ->setEntityLabelInPlural(t('Departments'));
    }

    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', t('ID', domain: 'department'))
            ->onlyOnDetail();
        yield TextField::new('name', t('Name', domain: 'department'));
        yield LookupSlotField::new('lookupSlot', t('Lookup slot', domain: 'department'))
            ->hideOnIndex();
        yield CollectionField::new('contactPeople', t('Contact people', domain: 'department'))
            ->renderExpanded()
            ->setRequired(true)
            ->setEntryType(ContactPersonType::class)
            ->setEntryIsComplex();
        yield DateTimeField::new('createdAt', t('Created at'))
            ->hideOnForm();
        yield DateTimeField::new('updatedAt', t('Updated at'))
            ->hideOnForm();
    }
}
