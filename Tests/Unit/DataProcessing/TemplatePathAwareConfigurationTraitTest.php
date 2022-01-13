<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "handlebars_components".
 *
 * Copyright (C) 2022 Elias Häußler <e.haeussler@familie-redlich.de>
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

namespace Fr\Typo3HandlebarsComponents\Tests\Unit\DataProcessing;

use Fr\Typo3Handlebars\Exception\InvalidTemplateFileException;
use Fr\Typo3HandlebarsComponents\Tests\Unit\Fixtures\TemplatePathAwareConfigurationTraitTestClass;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * TemplatePathAwareConfigurationTraitTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class TemplatePathAwareConfigurationTraitTest extends UnitTestCase
{
    /**
     * @var TemplatePathAwareConfigurationTraitTestClass
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new TemplatePathAwareConfigurationTraitTestClass();
    }

    /**
     * @test
     */
    public function getTemplatePathFromConfigurationThrowsExceptionIfTemplatePathIsNotConfigured(): void
    {
        $this->expectException(InvalidTemplateFileException::class);
        $this->expectExceptionCode(1641990786);
        $this->expectExceptionMessage('Missing or invalid template path in configuration array.');

        $this->subject->doGetTemplatePathFromConfiguration();
    }

    /**
     * @test
     */
    public function getTemplatePathFromConfigurationReturnsTemplatePathFromConfiguration(): void
    {
        $this->subject->configuration = [
            'userFunc.' => [
                'templatePath' => 'foo',
            ],
        ];

        self::assertSame('foo', $this->subject->doGetTemplatePathFromConfiguration());
    }
}
