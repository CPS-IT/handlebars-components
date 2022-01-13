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

namespace Fr\Typo3HandlebarsComponents\Tests\Unit\Service;

use Fr\Typo3HandlebarsComponents\Service\ConfigurationService;
use Fr\Typo3HandlebarsComponents\Tests\Unit\Fixtures\DummyConfigurationManager;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * ConfigurationServiceTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class ConfigurationServiceTest extends UnitTestCase
{
    /**
     * @var array<string, mixed>
     */
    protected $configuration = [
        'foo.' => [
            'baz' => 'hello!',
        ],
        'hello' => 'world',
    ];

    /**
     * @var ConfigurationService
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $configurationManager = new DummyConfigurationManager();
        $configurationManager->setConfiguration($this->configuration);
        $this->subject = new ConfigurationService($configurationManager);
    }

    /**
     * @test
     * @dataProvider getReturnsConfigurationAtGivenPathDataProvider
     * @param string $path
     * @param mixed $expected
     */
    public function getReturnsConfigurationAtGivenPath(string $path, $expected): void
    {
        self::assertSame($expected, $this->subject->get($path));
    }

    /**
     * @return \Generator<string, array{string, string|array<string, string>|null}>
     */
    public function getReturnsConfigurationAtGivenPathDataProvider(): \Generator
    {
        yield 'empty path' => ['', $this->configuration];
        yield 'invalid path' => ['foo', null];
        yield 'valid array path' => ['foo.', $this->configuration['foo.']];
        yield 'valid chained path' => ['foo.baz', $this->configuration['foo.']['baz']];
        yield 'valid path with one depth' => ['hello', $this->configuration['hello']];
    }
}
