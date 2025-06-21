<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Event;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Event\EntityLifecycleEventInterface;

/**
 * @author: Benjamin Leibinger <mail@leibinger.io>
 *
 * @template TEntity of object
 *
 * @implements EntityLifecycleEventInterface<TEntity>
 */
abstract class AbstractLifecycleEvent implements EntityLifecycleEventInterface
{
    /**
     * @var TEntity
     */
    protected $entityInstance;

    /**
     * @param TEntity $entityInstance
     */
    public function __construct(?object $entityInstance)
    {
        $this->entityInstance = $entityInstance;
    }

    public function getEntityInstance(): ?object
    {
        return $this->entityInstance;
    }
}
