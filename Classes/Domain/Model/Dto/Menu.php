<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "fr/handlebars_components".
 *
 * Copyright (C) 2021 Martin Adler <m.adler@familie-redlich.de>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace Fr\Typo3HandlebarsComponents\Domain\Model\Dto;

/**
 * Menu
 *
 * @author Martin Adler <m.adler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class Menu
{
    /**
     * @var MenuItem[] $items
     */
    protected $items;

    /**
     * @var string $type
     */
    protected $type;

    /**
     * @param string $type
     * @param MenuItem[] $items
     */
    public function __construct(string $type, array $items)
    {
        $this->type = $type;
        $this->items = $items;
    }

    /**
     * @return MenuItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param MenuItem[] $items
     * @return Menu
     */
    public function setItems(array $items): Menu
    {
        $this->items = $items;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Menu
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }
}
