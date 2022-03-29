<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "handlebars_components".
 *
 * Copyright (C) 2022 Elias Häußler <e.haeussler@familie-redlich.de>
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

namespace Fr\Typo3HandlebarsComponents\Tests\Unit\Fixtures;

use TYPO3\CMS\Core\Pagination\PaginatorInterface;

/**
 * DummyPaginator
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 * @internal
 */
final class DummyPaginator implements PaginatorInterface
{
    private int $currentPageNumber = 1;

    public function withItemsPerPage(int $itemsPerPage): PaginatorInterface
    {
        throw new \LogicException('This method is not implemented.', 1645185978);
    }

    public function withCurrentPageNumber(int $currentPageNumber): PaginatorInterface
    {
        $clone = clone $this;
        $clone->currentPageNumber = $currentPageNumber;

        return $clone;
    }

    /**
     * @return iterable<mixed>
     */
    public function getPaginatedItems(): iterable
    {
        throw new \LogicException('This method is not implemented.', 1645185967);
    }

    public function getNumberOfPages(): int
    {
        return 10;
    }

    public function getCurrentPageNumber(): int
    {
        return $this->currentPageNumber;
    }

    public function getKeyOfFirstPaginatedItem(): int
    {
        throw new \LogicException('This method is not implemented.', 1645185970);
    }

    public function getKeyOfLastPaginatedItem(): int
    {
        throw new \LogicException('This method is not implemented.', 1645185972);
    }
}
