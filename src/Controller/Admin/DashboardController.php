<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Role;
use App\Settings;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

use function Symfony\Component\Translation\t;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
final class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly Settings $settings,
    ) {
    }

    #[\Override]
    public function index(): Response
    {
        return $this->redirectToRoute('admin_department_index');
    }

    #[\Override]
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()->setTitle($this->settings->get('site_name'));
    }

    #[\Override]
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkTo(DepartmentCrudController::class, t('Departments'));
        if ($this->isGranted(Role::Administrator->value)) {
            yield MenuItem::linkTo(UserCrudController::class, t('Users'));
            yield MenuItem::linkTo(ActionLogEntryCrudController::class, t('Action log'));
        }
        yield MenuItem::linkTo(SettingCrudController::class, t('Settings'))->setPermission(Role::Administrator->value);
        if ('dev' === $this->getParameter('kernel.environment')) {
            yield MenuItem::linkTo(EntryCrudController::class, t('Entries'), icon: 'fa-brands fa-dev');
        }
        yield MenuItem::linkToUrl(t('Front page'), null, $this->generateUrl('app_default'));
    }
}
