<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Functional\SecureApp;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use EasyCorp\Bundle\EasyAdminBundle\EasyAdminBundle;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminRouteLoader;
use Symfony\Bundle\DebugBundle\DebugBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as SymfonyKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\UX\TwigComponent\TwigComponentBundle;
use Twig\Extra\TwigExtraBundle\TwigExtraBundle;

final class Kernel extends SymfonyKernel
{
    use MicroKernelTrait;

    public function __construct()
    {
        parent::__construct('test', false);
    }

    public function registerBundles(): iterable
    {
        return [
            new DebugBundle(),
            new DoctrineBundle(),
            new EasyAdminBundle(),
            new FrameworkBundle(),
            new SecurityBundle(),
            new TwigBundle(),
            new TwigComponentBundle(),
            new TwigExtraBundle(),
        ];
    }

    public function getCacheDir(): string
    {
        return $this->getProjectDir().'/var/cache/tests/SecureApp';
    }

    public function getLogDir(): string
    {
        return $this->getProjectDir().'/var/log/tests/SecureApp';
    }

    public function getProjectDir(): string
    {
        return \dirname(__DIR__);
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import(__DIR__.'/Controller/', 'attribute');
        $routes->import('.', AdminRouteLoader::ROUTE_LOADER_TYPE);
    }

    protected function configureContainer(ContainerConfigurator $container, LoaderInterface $loader): void
    {
        $container->parameters()
            ->set('locale', 'en');

        $services = $container->services()
            ->defaults()
            ->autowire()
            ->autoconfigure()
        ;
        $services
            ->load('EasyCorp\\Bundle\\EasyAdminBundle\\Tests\\Functional\\SecureApp\\', __DIR__.'/*')
            ->exclude('{Entity,Tests,Kernel.php}');
        $services
            ->load('EasyCorp\\Bundle\\EasyAdminBundle\\Tests\\Functional\\SecureApp\\Controller\\', __DIR__.'/Controller/')
            ->tag('controller.service_arguments');

        $container->extension('doctrine', [
            'dbal' => [
                'driver' => 'pdo_sqlite',
                'path' => '%kernel.cache_dir%/database.sqlite',
            ],

            'orm' => [
                'auto_generate_proxy_classes' => true,
                'controller_resolver' => [
                    'auto_mapping' => false,
                ],
                'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
                'auto_mapping' => true,
                'mappings' => [
                    'TestEntities' => [
                        'is_bundle' => false,
                        'type' => 'attribute',
                        'dir' => '%kernel.project_dir%/src/Entity',
                        'prefix' => 'EasyCorp\\Bundle\\EasyAdminBundle\\Tests\\Functional\\SecureApp\\Entity',
                        'alias' => 'app',
                    ],
                ],
            ],
        ]);

        $container->extension('framework', [
            'secret' => 'SECRET_1234',
            'csrf_protection' => true,
            'http_method_override' => true,
            'session' => [
                'handler_id' => null,
                'storage_factory_id' => 'session.storage.factory.mock_file',
            ],
            'test' => true,
            'profiler' => ['enabled' => true, 'collect' => false],
            'translator' => [
                'default_path' => '%kernel.project_dir%/translations',
                'fallbacks' => ['%locale%'],
            ],
        ]);

        if (self::MAJOR_VERSION < 7) {
            $container->extension('framework', [
                // Since symfony/framework-bundle 6.4: Not setting the "framework.handle_all_throwables" config option is deprecated. It will default to "true" in 7.0.
                'handle_all_throwables' => true,
                'session' => [
                    // Since symfony/framework-bundle 6.4: Not setting the "framework.session.cookie_secure" config option is deprecated. It will default to "auto" in 7.0.
                    'cookie_secure' => 'auto',
                    // Since symfony/framework-bundle 6.4: Not setting the "framework.session.cookie_samesite" config option is deprecated. It will default to "lax" in 7.0.
                    'cookie_samesite' => 'lax',
                ],
                'validation' => [
                    // Since symfony/framework-bundle 6.4: Not setting the "framework.validation.email_validation_mode" config option is deprecated. It will default to "html5" in 7.0.
                    'email_validation_mode' => 'html5',
                ],
                'php_errors' => [
                    // Since symfony/framework-bundle 6.4: Not setting the "framework.php_errors.log" config option is deprecated. It will default to "true" in 7.0.
                    'log' => true,
                ],
                'uid' => [
                    // Since symfony/framework-bundle 6.4: Not setting the "framework.uid.default_uuid_version" config option is deprecated. It will default to "7" in 7.0.
                    'default_uuid_version' => 7,
                    // Since symfony/framework-bundle 6.4: Not setting the "framework.uid.time_based_uuid_version" config option is deprecated. It will default to "7" in 7.0.
                    'time_based_uuid_version' => 7,
                ],
            ]);
        }

        $container->extension('twig', [
            'default_path' => '%kernel.project_dir%/templates',
        ]);

        $container->extension('twig_component', [
            'anonymous_template_directory' => 'components/',
        ]);

        $container->extension('security', [
            'password_hashers' => [
                InMemoryUser::class => 'plaintext',
            ],

            'providers' => [
                'test_users' => [
                    'memory' => [
                        'users' => [
                            'user' => [
                                'password' => '1234',
                                'roles' => ['ROLE_USER'],
                            ],
                            'admin' => [
                                'password' => '1234',
                                'roles' => ['ROLE_ADMIN'],
                            ],
                        ],
                    ],
                ],
            ],

            'firewalls' => [
                'secure_admin' => [
                    'pattern' => '^/admin',
                    'provider' => 'test_users',
                    'http_basic' => null,
                    'logout' => null,
                ],
            ],

            'role_hierarchy' => [
                'ROLE_ADMIN' => ['ROLE_USER'],
            ],

            'access_control' => [
                ['path' => '^/admin', 'roles' => ['ROLE_ADMIN']],
            ],
        ]);
    }
}
