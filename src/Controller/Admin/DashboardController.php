<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('mini ERP — Administration')
            ->setFaviconPath('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 128 128%22><text y=%221.2em%22 font-size=%2296%22>⚙️</text></svg>');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToUrl('Retour à l\'application', 'fa fa-arrow-left', $this->generateUrl('app_home'));

        yield MenuItem::section('Configuration');
        yield MenuItem::linkTo(SocieteCrudController::class, 'Société', 'fa fa-building');
        yield MenuItem::linkTo(ModuleCrudController::class, 'Modules', 'fa fa-puzzle-piece');

        yield MenuItem::section('Listes de référence');
        yield MenuItem::linkTo(CategorieContactCrudController::class, 'Catégories de contact', 'fa fa-tags');

        yield MenuItem::section('Utilisateurs & droits');
        yield MenuItem::linkTo(UserCrudController::class, 'Utilisateurs', 'fa fa-users');
        yield MenuItem::linkTo(RoleCrudController::class, 'Rôles', 'fa fa-user-shield');
        yield MenuItem::linkTo(PermissionCrudController::class, 'Permissions', 'fa fa-key');
    }
}
