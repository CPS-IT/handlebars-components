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

namespace Fr\Typo3HandlebarsComponents\Tests\Unit\Pagination;

use Fr\Typo3HandlebarsComponents\Pagination\NumberedPagination;
use Fr\Typo3HandlebarsComponents\Tests\Unit\Fixtures\DummyPaginator;
use Fr\Typo3HandlebarsComponents\Tests\Unit\Fixtures\PaginationTrait;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * NumberedPaginationTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class NumberedPaginationTest extends UnitTestCase
{
    use PaginationTrait;

    /**
     * @var NumberedPagination
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->buildPagination();
    }

    /**
     * @test
     * @dataProvider constructorCalculatesDisplayedPagesCorrectlyDependingOnCurrentPageNumberDataProvider
     *
     * @param array<int> $items
     * @param int $currentPageNumber
     * @param array<int|null> $expected
     */
    public function constructorCalculatesDisplayedPagesCorrectlyDependingOnCurrentPageNumber(
        array $items,
        int $currentPageNumber,
        array $expected
    ): void {
        $subject = $this->buildPagination($currentPageNumber, 1, 5, $items);

        self::assertEquals($expected, iterator_to_array($subject->getDisplayedPages()));
    }

    /**
     * @test
     * @dataProvider constructorIgnoresInvalidMaximumNumberOfLinksDuringCalculationOfDisplayedPagesDataProvider
     *
     * @param int $maximumNumberOfLinks,
     */
    public function constructorIgnoresInvalidMaximumNumberOfLinksDuringCalculationOfDisplayedPages(
        int $maximumNumberOfLinks
    ): void {
        $subject = $this->buildPagination(1, 1, $maximumNumberOfLinks);

        self::assertSame(range(1, 10), iterator_to_array($subject->getDisplayedPages()));
    }

    /**
     * @test
     */
    public function getPreviousPageNumberReturnsNullOnFirstPage(): void
    {
        self::assertNull($this->subject->getPreviousPageNumber());
    }

    /**
     * @test
     */
    public function getPreviousPageNumberReturnsPreviousPage(): void
    {
        $subject = $this->buildPagination(2);

        self::assertSame(1, $subject->getPreviousPageNumber());
    }

    /**
     * @test
     */
    public function getNextPageNumberReturnsNullOnLastPage(): void
    {
        $subject = $this->buildPagination(10);

        self::assertNull($subject->getNextPageNumber());
    }

    /**
     * @test
     */
    public function getNextPageNumberReturnsNextPage(): void
    {
        $subject = $this->buildPagination(9);

        self::assertSame(10, $subject->getNextPageNumber());
    }

    /**
     * @test
     */
    public function getFirstPageNumberReturnsAlwaysOne(): void
    {
        self::assertSame(1, $this->subject->getFirstPageNumber());
    }

    /**
     * @test
     */
    public function getLastPageNumberReturnsLastPageNumber(): void
    {
        self::assertSame(10, $this->subject->getLastPageNumber());
    }

    /**
     * @test
     */
    public function getStartRecordNumberReturnsZeroIfCurrentPageNumberIsNotInRange(): void
    {
        $paginator = new DummyPaginator();
        $paginator = $paginator->withCurrentPageNumber(0);
        $subject = new NumberedPagination($paginator);

        self::assertSame(0, $subject->getStartRecordNumber());
    }

    /**
     * @test
     */
    public function getStartRecordNumberReturnsStartRecordNumber(): void
    {
        self::assertSame(1, $this->subject->getStartRecordNumber());
    }

    /**
     * @test
     */
    public function getEndRecordNumberReturnsZeroIfCurrentPageNumberIsNotInRange(): void
    {
        $paginator = new DummyPaginator();
        $paginator = $paginator->withCurrentPageNumber(0);
        $subject = new NumberedPagination($paginator);

        self::assertSame(0, $subject->getEndRecordNumber());
    }

    /**
     * @test
     */
    public function getEndRecordNumberReturnsEndRecordNumber(): void
    {
        self::assertSame(1, $this->subject->getEndRecordNumber());
    }

    /**
     * @test
     */
    public function getPaginatedItemsReturnsPaginatedItems(): void
    {
        self::assertSame([1], iterator_to_array($this->subject->getPaginatedItems()));

        $subject = $this->buildPagination(2, 2);

        self::assertSame([3, 4], iterator_to_array($subject->getPaginatedItems()));
    }

    /**
     * @return \Generator<string, array{array<int>, int, array<int|null>}>
     */
    public function constructorCalculatesDisplayedPagesCorrectlyDependingOnCurrentPageNumberDataProvider(): \Generator
    {
        // Order of arguments:
        // 1. items
        // 2. currentPageNumber
        // 3. expected

        $items = range(1, 10);

        yield 'no items' => [
            [],
            1,
            [1],
        ];
        yield 'invalid page number' => [
            [1],
            2,
            [1],
        ];
        yield 'first page' => [
            $items,
            1,
            [1, 2, 3, 4, null, 10],
        ];
        yield 'second page' => [
            $items,
            2,
            [1, 2, 3, 4, null, 10],
        ];
        yield 'last page' => [
            $items,
            10,
            [1, null, 7, 8, 9, 10],
        ];
        yield 'second to last page' => [
            $items,
            9,
            [1, null, 7, 8, 9, 10],
        ];
        yield 'equal ranges to start and end' => [
            $items,
            5,
            [1, null, 4, 5, 6, null, 10],
        ];
        yield 'lower range to start' => [
            $items,
            3,
            [1, 2, 3, 4, null, 10],
        ];
        yield 'lower range to end' => [
            $items,
            8,
            [1, null, 7, 8, 9, 10],
        ];
    }

    /**
     * @return \Generator<string, array{int}>
     */
    public function constructorIgnoresInvalidMaximumNumberOfLinksDuringCalculationOfDisplayedPagesDataProvider(): \Generator
    {
        yield 'value too low' => [0];
        yield 'value too high' => [100];
    }
}
