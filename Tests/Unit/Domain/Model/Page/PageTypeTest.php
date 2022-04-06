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

namespace Fr\Typo3HandlebarsComponents\Tests\Unit\Domain\Model\Page;

use Fr\Typo3HandlebarsComponents\Domain\Model\Page\PageType;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * PageTypeTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class PageTypeTest extends UnitTestCase
{
    /**
     * @var PageType
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new PageType(PageType::STANDARD);
    }

    /**
     * @test
     */
    public function isTestsWhetherSubjectMatchesGivenPageType(): void
    {
        self::assertTrue($this->subject->is(PageType::STANDARD));
        self::assertFalse($this->subject->is(PageType::LINK));
        self::assertFalse($this->subject->is(PageType::SHORTCUT));
        self::assertFalse($this->subject->is(PageType::BACKEND_USER_SECTION));
        self::assertFalse($this->subject->is(PageType::MOUNTPOINT));
        self::assertFalse($this->subject->is(PageType::SPACER));
        self::assertFalse($this->subject->is(PageType::SYS_FOLDER));
        self::assertFalse($this->subject->is(PageType::RECYCLER));
    }

    /**
     * @test
     */
    public function getTypeReturnsType(): void
    {
        self::assertSame(PageType::STANDARD, $this->subject->getType());
    }

    /**
     * @test
     */
    public function stringRepresentationEqualsDefinedPageType(): void
    {
        self::assertEquals(PageType::STANDARD, (string)$this->subject);
    }

    /**
     * @test
     * @dataProvider dynamicMethodsCanBeCalledDataProvider
     */
    public function dynamicMethodsCanBeCalled(int $type, string $expectedMethod): void
    {
        $subject = new PageType($type);

        self::assertTrue($subject->$expectedMethod());
    }

    /**
     * @return \Generator<string, array{int, string}>
     */
    public function dynamicMethodsCanBeCalledDataProvider(): \Generator
    {
        yield 'standard' => [PageType::STANDARD, 'isStandard'];
        yield 'link' => [PageType::LINK, 'isLink'];
        yield 'shortcut' => [PageType::SHORTCUT, 'isShortcut'];
        yield 'backend user section' => [PageType::BACKEND_USER_SECTION, 'isBackendUserSection'];
        yield 'mountpoint' => [PageType::MOUNTPOINT, 'isMountpoint'];
        yield 'spacer' => [PageType::SPACER, 'isSpacer'];
        yield 'sys folder' => [PageType::SYS_FOLDER, 'isSysFolder'];
        yield 'recycler' => [PageType::RECYCLER, 'isRecycler'];
    }
}
