<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "handlebars_components".
 *
 * Copyright (C) 2023 Elias Häußler <e.haeussler@familie-redlich.de>
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

namespace Fr\Typo3HandlebarsComponents\Tests\Functional\Renderer\Template;

use Fr\Typo3Handlebars\Tests\Unit\HandlebarsTemplateResolverTrait;
use Fr\Typo3HandlebarsComponents\Renderer\Template\FlatTemplateResolver;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * FlatTemplateResolverTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 * @covers \Fr\Typo3HandlebarsComponents\Renderer\Template\FlatTemplateResolver
 */
final class FlatTemplateResolverTest extends FunctionalTestCase
{
    use HandlebarsTemplateResolverTrait;

    protected $testExtensionsToLoad = [
        'typo3conf/ext/handlebars_components/Tests/Functional/Fixtures/test_extension',
    ];

    protected $initializeDatabase = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->templateResolver = new FlatTemplateResolver($this->getTemplatePaths());
    }

    /**
     * @test
     */
    public function resolveTemplatePathRespectsTemplateVariant(): void
    {
        $expected = $this->instancePath . '/typo3conf/ext/test_extension/Resources/Templates/main-layout--variant.hbs';

        self::assertSame($expected, $this->templateResolver->resolveTemplatePath('@main-layout--variant'));
    }

    /**
     * @test
     */
    public function resolveTemplatePathReturnsBaseTemplateForNonExistingTemplateVariant(): void
    {
        $expected = $this->instancePath . '/typo3conf/ext/test_extension/Resources/Templates/main-layout.hbs';

        self::assertSame($expected, $this->templateResolver->resolveTemplatePath('@main-layout--non-existing-variant'));
    }

    public function getTemplateRootPath(): string
    {
        return 'EXT:test_extension/Resources/Templates/';
    }
}
