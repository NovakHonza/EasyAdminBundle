<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Tests\PrettyUrlsTestApplication\Entity\User;
use Symfony\Component\HttpFoundation\Response;

/**
 * @extends AbstractCrudController<User>
 */
#[AdminRoute('/user-editor', 'external_user_editor')]
class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            EmailField::new('email'),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('name')
            ->add('email');
    }

    #[AdminRoute('/custom/path-for-index', 'custom_route_for_index')]
    public function index(AdminContext $context): KeyValueStore|Response
    {
        return parent::index($context);
    }

    #[AdminRoute('/custom/path-for-detail/{entityId}')]
    public function detail(AdminContext $context): KeyValueStore|Response
    {
        return parent::detail($context);
    }

    #[AdminRoute(name: 'custom_route_for_new')]
    public function new(AdminContext $context): KeyValueStore|Response
    {
        return parent::new($context);
    }

    // this action doesn't use the #[AdminRoute] attribute on purpose to test default behavior
    public function edit(AdminContext $context): KeyValueStore|Response
    {
        return parent::edit($context);
    }

    #[AdminRoute('/bar/foo', 'foobar')]
    public function someCustomAction(): Response
    {
        return new Response('This is a custom action');
    }

    #[AdminRoute('/bar/bar', 'foofoo')]
    public function anotherCustomActionWithoutPropertyNames(): Response
    {
        return new Response('This is custom action with short attribute syntax');
    }

    // this custom action doesn't use the #[AdminRoute] attribute on purpose to test default behavior
    public function anotherCustomAction(): Response
    {
        return new Response('This is another custom action');
    }
}
