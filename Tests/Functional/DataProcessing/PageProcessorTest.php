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

namespace Cpsit\Typo3HandlebarsComponents\Tests\Functional\DataProcessing;

use Doctrine\DBAL\Result;
use Cpsit\Typo3HandlebarsComponents\Data\PageProvider;
use Cpsit\Typo3HandlebarsComponents\DataProcessing\PageProcessor;
use Cpsit\Typo3HandlebarsComponents\Domain\Factory\Page\PageFactory;
use Cpsit\Typo3HandlebarsComponents\Domain\Model\Page;
use Cpsit\Typo3HandlebarsComponents\Tests\Functional\Fixtures\DummyPageContentRenderer;
use Fr\Typo3HandlebarsComponents\Tests\Functional\Fixtures\DummyPresenter;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Page\PageLayoutResolver;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * PageProcessorTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class PageProcessorTest extends FunctionalTestCase
{
    protected PageProcessor $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->importDataSet(\dirname(__DIR__) . '/Fixtures/pages.xml');

        $queryBuilder = $this->getConnectionPool()->getQueryBuilderForTable('pages');
        $result = $queryBuilder->select('*')
            ->from('pages')
            ->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter(1, Connection::PARAM_INT)))
            ->execute();
        \assert($result instanceof Result);
        $pageRecord = $result->fetchAssociative();

        self::assertIsArray($pageRecord);

        $contentObjectRenderer = new ContentObjectRenderer();
        $contentObjectRenderer->start($pageRecord, 'pages');

        $provider = new PageProvider(
            new PageFactory(GeneralUtility::makeInstance(PageLayoutResolver::class)),
            new DummyPageContentRenderer()
        );

        $this->subject = new PageProcessor();
        $this->subject->setProvider($provider);
        $this->subject->setPresenter(new DummyPresenter());
        $this->subject->setContentObjectRenderer($contentObjectRenderer);
    }

    /**
     * @test
     */
    public function renderReturnsRenderedDataFromProvider(): void
    {
        $page = new Page(1, new Page\PageType(Page\PageType::STANDARD));
        $page->setLayout('pagets__1');
        $page->setTitle('Page 1');
        $expected = [
            'page' => $page,
            'content' => 'Hello world!',
            'pageHeader' => null,
            'pageFooter' => null,
        ];
        $actual = unserialize($this->subject->process('', []));

        self::assertEquals($expected, $actual);
    }
}
