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
    * @var MenuItem[]
    */
    private $subItems = [];

    /**
     * @var Link
     */
    private $link;

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

    /**
     * @param MenuItem[] $subItems
     * @return MenuItem
     */
    public function setSubItems(array $subItems): MenuItem
    {
        $this->subItems = $subItems;
        return $this;
    }

    /**
     * @return Link
     */
    public function getLink(): Link
    {
        return $this->link;
    }

    /**
     * @param Link $link
     * @return MenuItem
     */
    public function setLink(Link $link): MenuItem
    {
        $this->link = $link;
        return $this;
    }
}
