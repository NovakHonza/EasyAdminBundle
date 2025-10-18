<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Dto;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 *
 * @template TEntity of object = object
 */
class BatchActionDto
{
    /**
     * @param array<mixed>          $entityIds
     * @param class-string<TEntity> $entityFqcn
     */
    public function __construct(
        private readonly string $name,
        private readonly array $entityIds,
        private readonly string $entityFqcn,
        private readonly string $csrfToken,
    ) {
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

    /**
     * @return class-string<TEntity>
     */
    public function getEntityFqcn(): string
    {
        return $this->entityFqcn;
    }

    public function getCsrfToken(): string
    {
        return $this->csrfToken;
    }
}
