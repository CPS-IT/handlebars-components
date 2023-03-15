<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "handlebars_components".
 *
 * Copyright (C) 2021 Elias Häußler <e.haeussler@familie-redlich.de>
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

namespace Fr\Typo3HandlebarsComponents\Tests\Unit\Domain\Model\Dto;

use Fr\Typo3HandlebarsComponents\Domain\Model\Dto\Link;
use Fr\Typo3HandlebarsComponents\Domain\Model\Dto\Menu;
use Fr\Typo3HandlebarsComponents\Domain\Model\Dto\MenuItem;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * MenuTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class MenuTest extends UnitTestCase
{
    /**
     * @var MenuItem[]
     */
    protected array $items = [];
    protected Menu $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->items = [
            new MenuItem(new Link('foo', 'baz')),
        ];
        $this->subject = new Menu('foo', $this->items);
    }

    /**
     * @test
     */
    public function getItemsReturnsItems(): void
    {
        self::assertSame($this->items, $this->subject->getItems());
    }

    /**
     * @test
     */
    public function setItemsAppliesGivenItems(): void
    {
        $items = [
            clone $this->items[0],
        ];

        self::assertSame($items, $this->subject->setItems($items)->getItems());
    }

    /**
     * @test
     */
    public function hasItemsTestsWhetherMenuHasItems(): void
    {
        self::assertTrue($this->subject->hasItems());
        self::assertFalse($this->subject->setItems([])->hasItems());
    }

    /**
     * @test
     */
    public function addItemAddsGivenItemToMenu(): void
    {
        $additionalItem = new MenuItem(new Link('https://example.com', 'menu item #2'));
        $expected = [
            $this->items[0],
            $additionalItem,
        ];

        self::assertSame($expected, $this->subject->addItem($additionalItem)->getItems());
    }

    /**
     * @test
     */
    public function getTypeReturnsType(): void
    {
        self::assertSame('foo', $this->subject->getType());
    }

    /**
     * @test
     */
    public function setTypeAppliesGivenType(): void
    {
        self::assertSame('baz', $this->subject->setType('baz')->getType());
    }
}
