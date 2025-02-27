<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Functional\PrettyUrlsApp\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Tests\Functional\PrettyUrlsApp\Entity\BlogPost;
use EasyCorp\Bundle\EasyAdminBundle\Tests\Functional\PrettyUrlsApp\Entity\Category;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin/pretty/urls', routeName: 'admin_pretty')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('EasyAdmin Tests');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::subMenu('Blog', 'fas fa-blog')->setSubItems([
            MenuItem::linkToCrud('Categories', 'fas fa-tags', Category::class),
            MenuItem::linkToCrud('Blog Posts', 'far fa-file-lines', BlogPost::class),
        ]);
    }
}
