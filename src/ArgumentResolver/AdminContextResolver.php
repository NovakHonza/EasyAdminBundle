<?php

namespace EasyCorp\Bundle\EasyAdminBundle\ArgumentResolver;

use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Provider\AdminContextProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/*
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class AdminContextResolver implements ValueResolverInterface
{
    public function __construct(private readonly AdminContextProviderInterface $adminContextProvider)
    {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (AdminContext::class !== $argument->getType()) {
            return [];
        }

        yield $this->adminContextProvider->getContext();
    }
}
