<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ActionLogEntry;
use Doctrine\Common\Collections\Order;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

use function Symfony\Component\Translation\t;

final class ActionLogEntryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ActionLogEntry::class;
    }

    #[\Override]
    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular(t('Action'))
            ->setEntityLabelInPlural(t('Actions'))
            ->setDefaultSort(['createdAt' => Order::Descending->value]);
    }

    #[\Override]
    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->disable(Action::NEW, Action::EDIT, Action::DELETE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        yield ChoiceField::new('type', t('Type', domain: 'action_log_entry'));
        yield DateTimeField::new('createdAt', t('Created at'));
        yield AssociationField::new('createdBy', t('Created by'));
        yield CodeEditorField::new('context', t('Context', domain: 'action_log_entry'))->onlyOnDetail()->formatValue(
            static fn (array $value): string => (string) json_encode(
                $value,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
            ),
        );
    }
}
