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

namespace Fr\Typo3HandlebarsComponents\Tests\Functional\Renderer\Helper;

use Fr\Typo3Handlebars\Cache\NullCache;
use Fr\Typo3Handlebars\Renderer\HandlebarsRenderer;
use Fr\Typo3Handlebars\Tests\Unit\HandlebarsTemplateResolverTrait;
use Fr\Typo3HandlebarsComponents\Renderer\Helper\BlockHelper;
use Fr\Typo3HandlebarsComponents\Renderer\Helper\ContentHelper;
use Fr\Typo3HandlebarsComponents\Renderer\Helper\ExtendHelper;
use Fr\Typo3HandlebarsComponents\Renderer\Template\FlatTemplateResolver;
use Psr\Log\Test\TestLogger;
use Symfony\Component\EventDispatcher\EventDispatcher;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * ContentHelperTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class ContentHelperTest extends FunctionalTestCase
{
    use HandlebarsTemplateResolverTrait;

    protected $testExtensionsToLoad = [
        'typo3conf/ext/handlebars_components/Tests/Functional/Fixtures/test_extension',
    ];

    protected HandlebarsRenderer $renderer;
    protected TestLogger $logger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->logger = new TestLogger();
        $subject = new ContentHelper();
        $subject->setLogger($this->logger);

        $this->templateResolver = new FlatTemplateResolver($this->getTemplatePaths());
        $this->renderer = new HandlebarsRenderer(new NullCache(), new EventDispatcher(), $this->templateResolver);
        $this->renderer->registerHelper('extend', [new ExtendHelper($this->renderer), 'evaluate']);
        $this->renderer->registerHelper('content', [$subject, 'evaluate']);
        $this->renderer->registerHelper('block', [new BlockHelper(), 'evaluate']);
    }

    /**
     * @test
     */
    public function helperCanBeCalledFromExtendedLayout(): void
    {
        $actual = trim($this->renderer->render('@main-layout-extended', [
            'templateName' => '@main-layout',
        ]));
        $expected = implode(PHP_EOL, [
            'this is the main block:',
            '',
            '[ ]+main block',
            '[ ]+injected',
            '',
            'this is the second block:',
            '',
            '[ ]+injected',
            '[ ]+second block',
            '',
            'this is the third block:',
            '',
            '[ ]+injected',
            '',
            'this is the fourth block:',
            '',
            '[ ]+injected',
            '',
            'this is the end. bye bye',
        ]);

        self::assertMatchesRegularExpression('/^' . $expected . '$/', $actual);
    }

    /**
     * @test
     */
    public function helperCannotBeCalledOutsideOfExtendedLayout(): void
    {
        $this->renderer->render('@main-layout-content-only');

        self::assertTrue(
            $this->logger->hasError([
                'message' => 'Handlebars layout helper "content" can only be used within an "extend" helper block!',
                'context' => [
                    'name' => 'main',
                ],
            ])
        );
    }

    /**
     * @test
     * @dataProvider helperCanBeCalledToConditionallyRenderBlocksDataProvider
     */
    public function helperCanBeCalledToConditionallyRenderBlocks(bool $renderSecondBlock, string $expected): void
    {
        $actual = trim($this->renderer->render('@main-layout-extended-with-conditional-contents', [
            'templateName' => '@main-layout-conditional-block',
            'renderSecondBlock' => $renderSecondBlock,
        ]));

        self::assertMatchesRegularExpression('/^' . $expected . '$/', $actual);
    }

    /**
     * @return \Generator<string, array{bool, string}>
     */
    public function helperCanBeCalledToConditionallyRenderBlocksDataProvider(): \Generator
    {
        yield 'without second block' => [false, ''];
        yield 'with second block' => [true, 'main block\n+[ ]+second block'];
    }

    public function getTemplateRootPath(): string
    {
        return 'EXT:test_extension/Resources/Templates/';
    }
}
