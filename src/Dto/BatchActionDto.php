<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Dto;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class BatchActionDto
{
    private string $name;
    /** @var array<mixed> */
    private array $entityIds;
    private string $entityFqcn;
    private string $csrfToken;

    /**
     * @param array<mixed> $entityIds
     */
    public function __construct(string $name, array $entityIds, string $entityFqcn, string $csrfToken)
    {
        $this->name = $name;
        $this->entityIds = $entityIds;
        $this->entityFqcn = $entityFqcn;
        $this->csrfToken = $csrfToken;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array<mixed>
     */
    public function getEntityIds(): array
    {
        return $this->entityIds;
    }

    public function getEntityFqcn(): string
    {
        return $this->entityFqcn;
    }

    public function getCsrfToken(): string
    {
        return $this->csrfToken;
    }
}
