<?php

namespace EasyCorp\Bundle\EasyAdminBundle\DependencyInjection;

use EasyCorp\Bundle\EasyAdminBundle\Registry\AdminControllerRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Creates the services of the controller registries. They can't be defined as
 * normal services because they cause circular dependencies.
 * See https://github.com/EasyCorp/EasyAdminBundle/issues/3541.
 *
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class CreateControllerRegistriesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $this->createAdminControllerRegistryService($container);
        $this->createDashboardControllerRegistryService($container);
        $this->createCrudControllerRegistryService($container);
    }

    private function createAdminControllerRegistryService(ContainerBuilder $container): void
    {
        $dashboardControllersFqcn = array_keys($container->findTaggedServiceIds(EasyAdminExtension::TAG_DASHBOARD_CONTROLLER, true));
        $crudControllersFqcn = array_keys($container->findTaggedServiceIds(EasyAdminExtension::TAG_CRUD_CONTROLLER, true));

        $crudFqcnToEntityFqcnMap = [];
        foreach ($crudControllersFqcn as $controllerFqcn) {
            $crudFqcnToEntityFqcnMap[$controllerFqcn] = $controllerFqcn::getEntityFqcn();
        }

        // the service is defined with abstract_arg() placeholders:
        // here we only replace the dynamic arguments built from tagged services.
        $container->getDefinition(AdminControllerRegistry::class)
            ->replaceArgument(1, $crudFqcnToEntityFqcnMap)
            ->replaceArgument(2, $dashboardControllersFqcn);
    }
}
