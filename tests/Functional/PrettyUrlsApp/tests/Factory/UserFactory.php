<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Functional\PrettyUrlsApp\Tests\Factory;

use EasyCorp\Bundle\EasyAdminBundle\Tests\Functional\PrettyUrlsApp\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Tests\Functional\PrettyUrlsApp\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Category>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return User::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array
    {
        return [
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(User $user): void {})
        ;
    }
}
