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
use Fr\Typo3HandlebarsComponents\Domain\Model\Dto\MenuItem;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * MenuItemTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class MenuItemTest extends UnitTestCase
{
    /**
     * @var Link
     */
    protected $link;

    /**
     * @var MenuItem
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->link = new Link('foo', 'baz');
        $this->subject = new MenuItem($this->link);
    }

    /**
     * @test
     */
    public function getSubItemsReturnsSubItems(): void
    {
        $subItems = [
            clone $this->subject,
            clone $this->subject,
        ];

        self::assertSame([], $this->subject->getSubItems());
        self::assertSame($subItems, $this->subject->setSubItems($subItems)->getSubItems());
    }

    /**
     * @test
     */
    public function hasSubItemsTestsWhetherSubjectHasSubItems(): void
    {
        self::assertFalse($this->subject->hasSubItems());
        self::assertTrue($this->subject->setSubItems([clone $this->subject])->hasSubItems());
    }

    /**
     * @test
     */
    public function getLinkReturnsLink(): void
    {
        self::assertSame($this->link, $this->subject->getLink());
    }

    /**
     * @test
     */
    public function setLinkAppliesGivenLink(): void
    {
        $link = new Link('another', 'foo');

        self::assertSame($link, $this->subject->setLink($link)->getLink());
    }

    /**
     * @test
     */
    public function isActiveTestsWhetherSubjectIsActive(): void
    {
        self::assertFalse($this->subject->isActive());
        self::assertTrue($this->subject->setActive(true)->isActive());
    }

    /**
     * @test
     */
    public function isCurrentTestsWhetherSubjectIsCurrent(): void
    {
        self::assertFalse($this->subject->isCurrent());
        self::assertTrue($this->subject->setCurrent(true)->isCurrent());
    }
}
