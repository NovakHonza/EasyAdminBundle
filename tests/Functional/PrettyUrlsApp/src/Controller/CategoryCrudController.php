<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Functional\PrettyUrlsApp\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminAction;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Tests\Functional\PrettyUrlsApp\Entity\Category;
use Symfony\Component\HttpFoundation\Response;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
        ];
    }

    #[AdminAction(routeName: 'custom_action', routePath: 'custom/action')]
    public function customAction(): Response
    {
        return $this->render('category/custom_action.html.twig');
    }
}
