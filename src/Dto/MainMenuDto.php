<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Dto;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class MainMenuDto
{
    /**
     * @param MenuItemDto[] $items
     */
    public function __construct(private readonly array $items)
    {
    }

    /**
     * @return MenuItemDto[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
