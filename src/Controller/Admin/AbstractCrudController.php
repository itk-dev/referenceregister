<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Settings;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController as BaseAbstractCrudController;

/**
 * @extends BaseAbstractCrudController<object>
 */
abstract class AbstractCrudController extends BaseAbstractCrudController
{
    public function __construct(
        protected readonly Settings $settings,
    ) {
    }

    #[\Override]
    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->showEntityActionsInlined()
            ->setDateTimeFormat('yyyy/MM/dd HH:mm')
            ->setTimezone((string) $this->settings->get('app_timezone'));
    }

    #[\Override]
    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)->disable(Action::DELETE);
    }
}
