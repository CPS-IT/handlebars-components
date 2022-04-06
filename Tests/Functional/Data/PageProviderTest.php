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

namespace Fr\Typo3HandlebarsComponents\Tests\Functional\Data;

use Fr\Typo3HandlebarsComponents\Data\PageProvider;
use Fr\Typo3HandlebarsComponents\Data\Response\PageProviderResponse;
use Fr\Typo3HandlebarsComponents\Domain\Factory\Page\PageFactory;
use Fr\Typo3HandlebarsComponents\Tests\Functional\Fixtures\DummyPageContentRenderer;
use Fr\Typo3HandlebarsComponents\Tests\Functional\Fixtures\DummyPageFooter;
use Fr\Typo3HandlebarsComponents\Tests\Functional\Fixtures\DummyPageFooterFactory;
use Fr\Typo3HandlebarsComponents\Tests\Functional\Fixtures\DummyPageHeader;
use Fr\Typo3HandlebarsComponents\Tests\Functional\Fixtures\DummyPageHeaderFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Page\PageLayoutResolver;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * PageProviderTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class PageProviderTest extends FunctionalTestCase
{
    /**
     * @var PageProvider
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->importDataSet(\dirname(__DIR__) . '/Fixtures/pages.xml');

        $this->subject = new PageProvider(
            new PageFactory(GeneralUtility::makeInstance(PageLayoutResolver::class)),
            new DummyPageContentRenderer(),
            new DummyPageHeaderFactory(),
            new DummyPageFooterFactory()
        );
        $this->subject->setContentObjectRenderer(new ContentObjectRenderer());
    }

    /**
     * @test
     */
    public function getReturnsPageProviderResponse(): void
    {
        $data = [
            'uid' => 2,
            'title' => 'foo',
            'subtitle' => 'baz',
            'doktype' => 1,
        ];

        $actual = $this->subject->get($data);

        self::assertInstanceOf(PageProviderResponse::class, $actual);

        self::assertSame(2, $actual->getPage()->getId());
        self::assertTrue($actual->getPage()->getPageType()->isStandard());
        self::assertSame('foo', $actual->getPage()->getTitle());
        self::assertSame('baz', $actual->getPage()->getSubtitle());
        self::assertSame('pagets__2', $actual->getPage()->getLayout());
        self::assertSame('Hello world!', $actual->getContent());
        self::assertInstanceOf(DummyPageHeader::class, $actual->getPageHeader());
        self::assertEquals($actual->getPage()->getId(), (string)$actual->getPageHeader());
        self::assertInstanceOf(DummyPageFooter::class, $actual->getPageFooter());
        self::assertEquals($actual->getPage()->getId(), (string)$actual->getPageFooter());
    }
}
