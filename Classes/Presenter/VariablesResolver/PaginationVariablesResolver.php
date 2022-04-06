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

namespace Fr\Typo3HandlebarsComponents\Presenter\VariablesResolver;

use Fr\Typo3HandlebarsComponents\Pagination\Linker\PaginationLinkerInterface;
use Fr\Typo3HandlebarsComponents\Pagination\NumberedPagination;

/**
 * PaginationVariablesResolver
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class PaginationVariablesResolver
{
    /**
     * @return array<string, mixed>
     */
    public function resolve(
        NumberedPagination $pagination,
        PaginationLinkerInterface $linker,
        string $placeholder = '…'
    ): array {
        $currentPageNumber = $pagination->getCurrentPageNumber();
        $variables = [
            'items' => [],
        ];

        // If only one page is available, we won't show any pagination items
        if ($pagination->getLastPageNumber() === $pagination->getFirstPageNumber()) {
            return $variables;
        }

        // Otherwise, create a pagination item for each displayed page
        foreach ($pagination->getDisplayedPages() as $page) {
            $pageVariables = [
                'label' => $page ?? $placeholder,
            ];

            if (\is_int($page)) {
                $pageVariables['link'] = $linker->buildUrlForPage($pagination, $page);
                $pageVariables['current'] = $page === $currentPageNumber;
            }

            $variables['items'][] = $pageVariables;
        }

        return $variables;
    }
}
