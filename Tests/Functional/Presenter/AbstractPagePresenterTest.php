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

namespace Fr\Typo3HandlebarsComponents\Tests\Functional\Presenter;

use Fr\Typo3Handlebars\Data\Response\SimpleProviderResponse;
use Fr\Typo3Handlebars\Exception\UnableToPresentException;
use Fr\Typo3HandlebarsComponents\Data\Response\PageProviderResponse;
use Fr\Typo3HandlebarsComponents\Domain\Model\Page;
use Fr\Typo3HandlebarsComponents\Tests\Functional\Fixtures\AbstractPagePresenterTestClass;
use Fr\Typo3HandlebarsComponents\Tests\Functional\Fixtures\DummyRenderer;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * AbstractPagePresenterTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class AbstractPagePresenterTest extends FunctionalTestCase
{
    protected PageRenderer $pageRenderer;
    protected AbstractPagePresenterTestClass $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $this->subject = new AbstractPagePresenterTestClass(new DummyRenderer(), $this->pageRenderer);
    }

    /**
     * @test
     */
    public function presentThrowsExceptionIfUnsupportedProviderResponseIsGiven(): void
    {
        $providerResponse = new SimpleProviderResponse();

        $this->expectException(UnableToPresentException::class);
        $this->expectExceptionCode(1616155696);

        $this->subject->present($providerResponse);
    }

    /**
     * @test
     */
    public function presentReturnsRenderedTemplate(): void
    {
        $providerResponse = new PageProviderResponse(new Page(1, new Page\PageType(Page\PageType::STANDARD)));
        $providerResponse->setContent('Hello world!');

        $expected = [
            'templatePath' => '@cms',
            'data' => [
                'templateName' => '@foo',
                'contentName' => 'mainContent',
                'renderedContent' => 'Hello world!',
                'foo' => 'baz',
            ],
        ];
        $actual = $this->subject->present($providerResponse);

        self::assertJson($actual);

        $json = json_decode($actual, true);
        $renderedPage = $this->pageRenderer->render();

        self::assertSame($expected, $json);
        self::assertStringContainsString('<!-- header assets -->', $renderedPage);
        self::assertStringContainsString('<!-- footer assets -->', $renderedPage);
    }
}
