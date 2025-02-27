<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Functional\PrettyUrlsApp\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Tests\Functional\PrettyUrlsApp\Entity\BlogPost;

class BlogPostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BlogPost::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            BooleanField::new('published'),
        ];
    }
}
