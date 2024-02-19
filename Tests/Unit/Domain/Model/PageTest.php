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

namespace Cpsit\Typo3HandlebarsComponents\Tests\Unit\Domain\Model;

use Cpsit\Typo3HandlebarsComponents\Domain\Model\Page;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * PageTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class PageTest extends UnitTestCase
{
    protected Page\PageType $pageType;
    protected Page $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pageType = new Page\PageType(Page\PageType::STANDARD);
        $this->subject = new Page(1, $this->pageType);
    }

    /**
     * @test
     */
    public function getIdReturnsId(): void
    {
        self::assertSame(1, $this->subject->getId());
    }

    /**
     * @test
     */
    public function getPageTypeReturnsPageType(): void
    {
        self::assertSame($this->pageType, $this->subject->getPageType());
    }

    /**
     * @test
     */
    public function getTitleReturnsTitle(): void
    {
        self::assertSame('', $this->subject->getTitle());
        self::assertSame('foo', $this->subject->setTitle('foo')->getTitle());
    }

    /**
     * @test
     */
    public function getSubtitleReturnsSubtitle(): void
    {
        self::assertSame('', $this->subject->getSubtitle());
        self::assertSame('foo', $this->subject->setSubtitle('foo')->getSubtitle());
    }

    /**
     * @test
     */
    public function getLayoutReturnsLayout(): void
    {
        self::assertSame('default', $this->subject->getLayout());
        self::assertSame('foo', $this->subject->setLayout('foo')->getLayout());
    }
}
