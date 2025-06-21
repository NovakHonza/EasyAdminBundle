<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Provider;

use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Context\AdminContextInterface;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Provider\AdminContextProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class AdminContextProvider implements AdminContextProviderInterface
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function hasContext(): bool
    {
        $currentRequest = $this->requestStack->getCurrentRequest();

        return null !== $currentRequest && $currentRequest->attributes->has(EA::CONTEXT_REQUEST_ATTRIBUTE);
    }

    public function getContext(): ?AdminContextInterface
    {
        return $this->requestStack->getCurrentRequest()?->get(EA::CONTEXT_REQUEST_ATTRIBUTE);
    }

    public function getRequest(): Request
    {
        trigger_deprecation(
            'easycorp/easyadmin-bundle',
            '4.25.0',
            'The "%s" method is deprecated and will be removed in EasyAdmin 5.0.0. Use the method with the same name from the "EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext" class instead. This deprecation may have been triggered by the usage of the global "ea" variable in a Twig template, which is also deprecated. Use the equivalent "ea()" Twig function instead.',
            __METHOD__
        );

        return $this->getContext(true)->getRequest();
    }
}
