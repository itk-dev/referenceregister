<?php

namespace App\Controller\Admin;

use App\Settings;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

use function Symfony\Component\Translation\t;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly Settings $settings,
    ) {
    }

    #[\Override]
    public function index(): Response
    {
        return $this->redirectToRoute('admin_setting_index');
    }

    #[\Override]
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle($this->settings->get('site_name'));
    }

    #[\Override]
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkTo(SettingCrudController::class, t('Settings'));
        yield MenuItem::linkToUrl(t('Frontpage'), null, $this->generateUrl('app_default'));
    }
}
