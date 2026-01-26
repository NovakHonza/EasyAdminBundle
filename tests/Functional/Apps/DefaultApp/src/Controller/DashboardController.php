<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Functional\Apps\DefaultApp\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Tests\Functional\Apps\DefaultApp\Entity\BlogPost;
use EasyCorp\Bundle\EasyAdminBundle\Tests\Functional\Apps\DefaultApp\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Tests\Functional\Apps\DefaultApp\Entity\Synthetic\ActionTestEntity;
use EasyCorp\Bundle\EasyAdminBundle\Tests\Functional\Apps\DefaultApp\Entity\Synthetic\BatchActionTestEntity;
use EasyCorp\Bundle\EasyAdminBundle\Tests\Functional\Apps\DefaultApp\Entity\Synthetic\DefaultCrudTestEntity;
use EasyCorp\Bundle\EasyAdminBundle\Tests\Functional\Apps\DefaultApp\Entity\Synthetic\FieldRelatedEntity;
use EasyCorp\Bundle\EasyAdminBundle\Tests\Functional\Apps\DefaultApp\Entity\Synthetic\FieldTestEntity;
use EasyCorp\Bundle\EasyAdminBundle\Tests\Functional\Apps\DefaultApp\Entity\Synthetic\FilterRelatedEntity;
use EasyCorp\Bundle\EasyAdminBundle\Tests\Functional\Apps\DefaultApp\Entity\Synthetic\FilterTestEntity;
use EasyCorp\Bundle\EasyAdminBundle\Tests\Functional\Apps\DefaultApp\Entity\Synthetic\FormTestEntity;
use EasyCorp\Bundle\EasyAdminBundle\Tests\Functional\Apps\DefaultApp\Entity\Synthetic\SearchTestEntity;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('EasyAdmin Tests');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Categories', 'fas fa-tags', Category::class);
        yield MenuItem::linkToCrud('Blog Posts', 'fas fa-tags', BlogPost::class);

        // synthetic test entities
        yield MenuItem::section('Synthetic Tests');
        yield MenuItem::linkToCrud('Field Tests', 'fas fa-flask', FieldTestEntity::class);
        yield MenuItem::linkToCrud('Field Related Entities', 'fas fa-link', FieldRelatedEntity::class);
        yield MenuItem::linkToCrud('Filter Tests', 'fas fa-filter', FilterTestEntity::class);
        yield MenuItem::linkToCrud('Filter Related Entities', 'fas fa-link', FilterRelatedEntity::class);
        yield MenuItem::linkToCrud('Form Layout Tests', 'fas fa-th-large', FormTestEntity::class);
        yield MenuItem::linkToCrud('Batch Action Tests', 'fas fa-tasks', BatchActionTestEntity::class);
        yield MenuItem::linkToCrud('Default CRUD Tests', 'fas fa-cog', DefaultCrudTestEntity::class);
        yield MenuItem::linkToCrud('Search Tests', 'fas fa-search', SearchTestEntity::class);
        yield MenuItem::linkToCrud('Action Tests', 'fas fa-mouse-pointer', ActionTestEntity::class);
    }
}
