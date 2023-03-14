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
use Fr\Typo3HandlebarsComponents\Renderer\Helper\ExtendHelper;
use Fr\Typo3HandlebarsComponents\Renderer\Template\FlatTemplateResolver;
use Fr\Typo3HandlebarsComponentsTestExtension\JsonHelper;
use Symfony\Component\EventDispatcher\EventDispatcher;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * ExtendHelperTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class ExtendHelperTest extends FunctionalTestCase
{
    use HandlebarsTemplateResolverTrait;

    protected $testExtensionsToLoad = [
        'typo3conf/ext/handlebars_components/Tests/Functional/Fixtures/test_extension',
    ];

    protected HandlebarsRenderer $renderer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->templateResolver = new FlatTemplateResolver($this->getTemplatePaths());
        $this->renderer = new HandlebarsRenderer(new NullCache(), new EventDispatcher(), $this->templateResolver);
        $this->renderer->registerHelper('extend', [new ExtendHelper($this->renderer), 'evaluate']);
        $this->renderer->registerHelper('jsonEncode', [new JsonHelper(), 'encode']);
    }

    /**
     * @test
     */
    public function helperCanBeCalledWithoutCustomContext(): void
    {
        $actual = trim($this->renderer->render('@simple-layout-extended'));
        $expected = [];

        self::assertJson($actual);

        $json = json_decode($actual, true);
        unset($json['_layoutStack']);

        self::assertSame($expected, $json);
    }

    /**
     * @test
     */
    public function helperCanBeCalledWithCustomContext(): void
    {
        $actual = trim($this->renderer->render('@simple-layout-extended-with-context', [
            'customContext' => [
                'foo' => 'baz',
            ],
        ]));
        $expected = [
            'customContext' => [
                'foo' => 'baz',
            ],
            'foo' => 'baz',
        ];

        self::assertJson($actual);

        $json = json_decode($actual, true);
        unset($json['_layoutStack']);

        self::assertSame($expected, $json);
    }

    /**
     * @test
     */
    public function helperReplacesVariablesCorrectlyInAllContexts(): void
    {
        $actual = trim($this->renderer->render('@simple-layout-extended-with-context', [
            'foo' => 123,
            'customContext' => [
                'foo' => 456,
            ],
        ]));

        $expected = [
            'foo' => 456,
            'customContext' => [
                'foo' => 456,
            ],
        ];

        self::assertJson($actual);

        $json = json_decode($actual, true);
        unset($json['_layoutStack']);

        self::assertSame($expected, $json);
    }

    public function getTemplateRootPath(): string
    {
        return 'EXT:test_extension/Resources/Templates/';
    }
}
