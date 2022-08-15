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

namespace Fr\Typo3HandlebarsComponents\Pagination;

use TYPO3\CMS\Core\Pagination\PaginationInterface;
use TYPO3\CMS\Core\Pagination\PaginatorInterface;

/**
 * NumberedPagination
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class NumberedPagination implements PaginationInterface
{
    protected const MINIMUM_NUMBER_OF_LINKS = 5;
    protected const DEFAULT_MAXIMUM_NUMBER_OF_LINKS = 5;

    /**
     * @var PaginatorInterface
     */
    protected $paginator;

    /**
     * @var int
     */
    protected $maximumNumberOfLinks;

    /**
     * @var array<int|null>
     */
    protected $displayedPages;

    public function __construct(PaginatorInterface $paginator, int $maximumNumberOfLinks = null)
    {
        $this->paginator = $paginator;
        $this->maximumNumberOfLinks = max(0, $maximumNumberOfLinks ?? self::DEFAULT_MAXIMUM_NUMBER_OF_LINKS);

        $this->calculateDisplayedPages();
    }

    public function getPreviousPageNumber(): ?int
    {
        $previousPage = $this->getCurrentPageNumber() - 1;

        return $this->isPageInRange($previousPage) ? $previousPage : null;
    }

    public function getNextPageNumber(): ?int
    {
        $nextPage = $this->getCurrentPageNumber() + 1;

        return $this->isPageInRange($nextPage) ? $nextPage : null;
    }

    public function getFirstPageNumber(): int
    {
        return 1;
    }

    public function getLastPageNumber(): int
    {
        return $this->paginator->getNumberOfPages();
    }

    public function getStartRecordNumber(): int
    {
        if (!$this->isPageInRange()) {
            return 0;
        }

        return $this->paginator->getKeyOfFirstPaginatedItem() + 1;
    }

    public function getEndRecordNumber(): int
    {
        if (!$this->isPageInRange()) {
            return 0;
        }

        return $this->paginator->getKeyOfLastPaginatedItem() + 1;
    }

    public function getCurrentPageNumber(): int
    {
        return $this->paginator->getCurrentPageNumber();
    }

    /**
     * @return \Generator<mixed>
     */
    public function getPaginatedItems(): \Generator
    {
        yield from $this->paginator->getPaginatedItems();
    }

    /**
     * @return \Generator<int|null>
     */
    public function getDisplayedPages(): \Generator
    {
        yield from $this->displayedPages;
    }

    /**
     * @internal Only to be used in testing context. Do not access from anywhere else!
     * @codeCoverageIgnore
     */
    public function getPaginator(): PaginatorInterface
    {
        return $this->paginator;
    }

    protected function calculateDisplayedPages(): void
    {
        $currentPageNumber = $this->getCurrentPageNumber();
        $firstPageNumber = $this->getFirstPageNumber();
        $lastPageNumber = $this->getLastPageNumber();

        // Use all pages if more links should be displayed than pages are available
        // or if the minimum number of displayed links is not reached. If only one
        // page exists, then only this page is displayed.
        if (
            $this->maximumNumberOfLinks >= $this->paginator->getNumberOfPages()
            || $this->maximumNumberOfLinks < self::MINIMUM_NUMBER_OF_LINKS
            || $firstPageNumber === $lastPageNumber
        ) {
            $this->displayedPages = range($firstPageNumber, $lastPageNumber);

            return;
        }

        // Calculate range from current page to start and end. This is necessary to
        // calculate the real ranges based on the "default" ones that ignore start
        // and end boundaries.
        $currentStartRange = $currentPageNumber - $firstPageNumber;
        $currentEndRange = $lastPageNumber - $currentPageNumber;

        if ($currentStartRange === 0) {
            // We're on the first page
            $firstDisplayedPage = $firstPageNumber;
            $lastDisplayedPage = $firstDisplayedPage + $this->maximumNumberOfLinks - 1;
        } elseif ($currentEndRange === 0) {
            // We're on the last page
            $lastDisplayedPage = $lastPageNumber;
            $firstDisplayedPage = $lastDisplayedPage - ($this->maximumNumberOfLinks - 1);
        } else {
            // Calculate perfect range to start and end. If the resulting number is odd,
            // it will be rounded in the next step, depending on the current page number's
            // position within the pagination.
            $perfectRange = ($this->maximumNumberOfLinks - 1) / 2;

            if ($currentStartRange > $currentEndRange) {
                // We're closer to the end
                $startRange = (int)floor($perfectRange);
                $endRange = $this->maximumNumberOfLinks - 1 - $startRange;
            } else {
                // We're closer to the start
                $endRange = (int)floor($perfectRange);
                $startRange = $this->maximumNumberOfLinks - 1 - $endRange;
            }

            $firstDisplayedPage = $currentPageNumber - $startRange;
            $lastDisplayedPage = $currentPageNumber + $endRange;

            // Correct ranges if calculated first page or last page are outside the
            // current page boundaries.
            if ($lastDisplayedPage > $lastPageNumber) {
                $moveToStart = $lastDisplayedPage - $lastPageNumber;
                $firstDisplayedPage -= $moveToStart;
                $lastDisplayedPage -= $moveToStart;
            } elseif ($firstDisplayedPage < $firstPageNumber) {
                $moveToEnd = $firstPageNumber - $firstDisplayedPage;
                $firstDisplayedPage += $moveToEnd;
                $lastDisplayedPage += $moveToEnd;
            }
        }

        // Build range from calculated and normalized page ranges
        $calculatedRange = range($firstDisplayedPage, $lastDisplayedPage);

        // Enforce visibility of first page and last page
        if (reset($calculatedRange) !== $firstPageNumber) {
            array_splice($calculatedRange, 0, 1, [$firstPageNumber, null]);
        }
        if (end($calculatedRange) !== $lastPageNumber) {
            array_splice($calculatedRange, -1, 1, [null, $lastPageNumber]);
        }

        $this->displayedPages = $calculatedRange;
    }

    protected function isPageInRange(int $pageNumber = null): bool
    {
        $pageNumber ??= $this->paginator->getCurrentPageNumber();

        return $pageNumber >= $this->getFirstPageNumber() && $pageNumber <= $this->getLastPageNumber();
    }
}
