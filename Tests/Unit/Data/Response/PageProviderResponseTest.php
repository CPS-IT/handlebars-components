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

namespace Fr\Typo3HandlebarsComponents\Tests\Unit\Data\Response;

use Fr\Typo3HandlebarsComponents\Data\Response\PageProviderResponse;
use Fr\Typo3HandlebarsComponents\Domain\Model\Page;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * PageProviderResponseTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class PageProviderResponseTest extends UnitTestCase
{
    protected Page $page;
    protected PageProviderResponse $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->page = new Page(1, new Page\PageType(Page\PageType::STANDARD));
        $this->subject = new PageProviderResponse($this->page);
    }

    /**
     * @test
     */
    public function getPageReturnsPage(): void
    {
        self::assertSame($this->page, $this->subject->getPage());
    }

    /**
     * @test
     */
    public function getContentReturnsContent(): void
    {
        self::assertSame('', $this->subject->getContent());
        self::assertSame('foo', $this->subject->setContent('foo')->getContent());
    }

    /**
     * @test
     */
    public function getPageHeaderReturnsPageHeader(): void
    {
        $pageHeader = new class() implements Page\PageHeaderInterface {
        };

        self::assertNull($this->subject->getPageHeader());
        self::assertSame($pageHeader, $this->subject->setPageHeader($pageHeader)->getPageHeader());
    }

    /**
     * @test
     */
    public function getPageFooterReturnsPageFooter(): void
    {
        $pageFooter = new class() implements Page\PageFooterInterface {
        };

        self::assertNull($this->subject->getPageFooter());
        self::assertSame($pageFooter, $this->subject->setPageFooter($pageFooter)->getPageFooter());
    }

    /**
     * @test
     */
    public function toArrayReturnsArrayRepresentationOfSubject(): void
    {
        $pageHeader = new class() implements Page\PageHeaderInterface {
        };
        $pageFooter = new class() implements Page\PageFooterInterface {
        };

        $this->subject->setContent('foo');
        $this->subject->setPageHeader($pageHeader);
        $this->subject->setPageFooter($pageFooter);

        $expected = [
            'page' => $this->page,
            'content' => 'foo',
            'pageHeader' => $pageHeader,
            'pageFooter' => $pageFooter,
        ];

        self::assertSame($expected, $this->subject->toArray());
    }
}
