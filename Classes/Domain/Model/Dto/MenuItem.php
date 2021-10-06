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
 * MenuItem
 *
 * @author Martin Adler <m.adler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class MenuItem
{
    /**
    * @var self[]
    */
    protected $subItems = [];

    /**
     * @var Link
     */
    protected $link;

    /**
     * @var bool
     */
    protected $active = false;

    /**
     * @var bool
     */
    protected $current = false;

    public function __construct(Link $link)
    {
        $this->link = $link;
    }

    /**
     * @return MenuItem[]
     */
    public function getSubItems(): array
    {
        return $this->subItems;
    }

    public function hasSubItems(): bool
    {
        return $this->subItems !== [];
    }

    /**
     * @param self[] $subItems
     * @return self
     */
    public function setSubItems(array $subItems): self
    {
        $this->subItems = $subItems;
        return $this;
    }

    public function getLink(): Link
    {
        return $this->link;
    }

    public function setLink(Link $link): self
    {
        $this->link = $link;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }

    public function isCurrent(): bool
    {
        return $this->current;
    }

    public function setCurrent(bool $current): self
    {
        $this->current = $current;
        return $this;
    }
}
