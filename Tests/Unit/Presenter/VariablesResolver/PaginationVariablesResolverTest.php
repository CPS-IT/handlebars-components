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

namespace Fr\Typo3HandlebarsComponents\Tests\Unit\Presenter\VariablesResolver;

use Fr\Typo3HandlebarsComponents\Presenter\VariablesResolver\PaginationVariablesResolver;
use Fr\Typo3HandlebarsComponents\Tests\Unit\Fixtures\DummyPaginationLinker;
use Fr\Typo3HandlebarsComponents\Tests\Unit\Fixtures\PaginationTrait;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * PaginationVariablesResolverTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class PaginationVariablesResolverTest extends UnitTestCase
{
    use PaginationTrait;

    protected PaginationVariablesResolver $subject;
    protected DummyPaginationLinker $linker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new PaginationVariablesResolver();
        $this->linker = new DummyPaginationLinker();
    }

    /**
     * @test
     */
    public function resolveReturnsArrayWithEmptyItemsIfOnlyOnePageIsAvailableInPagination(): void
    {
        $pagination = $this->buildPagination(1, 1, 5, []);

        $expected = [
            'items' => [],
        ];

        self::assertSame($expected, $this->subject->resolve($pagination, $this->linker));
    }

    /**
     * @test
     */
    public function resolveReturnsArrayWithItemsAndPlaceholders(): void
    {
        $pagination = $this->buildPagination(5, 1, 5, range(1, 10));

        $expected = [
            'items' => [
                [
                    'label' => 1,
                    'link' => '1',
                    'current' => false,
                ],
                [
                    'label' => '…',
                ],
                [
                    'label' => 4,
                    'link' => '4',
                    'current' => false,
                ],
                [
                    'label' => 5,
                    'link' => '5',
                    'current' => true,
                ],
                [
                    'label' => 6,
                    'link' => '6',
                    'current' => false,
                ],
                [
                    'label' => '…',
                ],
                [
                    'label' => 10,
                    'link' => '10',
                    'current' => false,
                ],
            ],
        ];

        self::assertSame($expected, $this->subject->resolve($pagination, $this->linker));
    }
}
