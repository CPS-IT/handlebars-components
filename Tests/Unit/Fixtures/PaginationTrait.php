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

namespace Cpsit\Typo3HandlebarsComponents\Tests\Unit\Fixtures;

use Cpsit\Typo3HandlebarsComponents\Pagination\NumberedPagination;
use TYPO3\CMS\Core\Pagination\ArrayPaginator;

/**
 * PaginationTrait
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
trait PaginationTrait
{
    /**
     * @param list<int>|null $items
     */
    protected function buildPagination(
        int $currentPageNumber = 1,
        int $itemsPerPage = 1,
        int $maximumNumberOfLinks = 5,
        array $items = null
    ): NumberedPagination {
        $paginator = new ArrayPaginator($items ?? range(1, 10), $currentPageNumber, $itemsPerPage);

        return new NumberedPagination($paginator, $maximumNumberOfLinks);
    }
}
