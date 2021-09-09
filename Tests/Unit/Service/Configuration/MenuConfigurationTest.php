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

namespace Fr\Typo3HandlebarsComponents\Tests\Unit\Service\Configuration;

use Fr\Typo3HandlebarsComponents\Exception\InvalidConfigurationException;
use Fr\Typo3HandlebarsComponents\Service\Configuration\MenuConfiguration;
use TYPO3\CMS\Frontend\DataProcessing\MenuProcessor;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * MenuConfigurationTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class MenuConfigurationTest extends UnitTestCase
{
    /**
     * @var array<string|int, string|array>
     */
    protected $configuration = [
        '10' => 'USER',
        '10.' => [
            'userFunc' => 'foo->baz',
            'userFunc.' => [
                'foo' => 'baz',
            ],
        ],
    ];

    /**
     * @test
     */
    public function constructorThrowsExceptionOnInvalidTypoScriptConfiguration(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionCode(1631118231);

        new MenuConfiguration(['foo.' => 'baz']);
    }

    /**
     * @test
     */
    public function constructorThrowsExceptionOnMissingDataProcessorForCustomMenus(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionCode(1631118231);

        new MenuConfiguration([], MenuConfiguration::CUSTOM);
    }

    /**
     * @test
     */
    public function directoryReturnsConfigurationForMenuWithSpecialDirectory(): void
    {
        self::assertSame([
            'special' => 'directory',
            'special.' => [
                'value' => 'auto',
            ],
            'levels' => 1,
        ], MenuConfiguration::directory()->getTypoScriptConfiguration());

        self::assertSame([
            'special' => 'directory',
            'special.' => [
                'value' => 1,
            ],
            'levels' => 1,
        ], MenuConfiguration::directory(1)->getTypoScriptConfiguration());

        self::assertSame([
            'special' => 'directory',
            'special.' => [
                'value' => 1,
            ],
            'levels' => 2,
        ], MenuConfiguration::directory(1, 2)->getTypoScriptConfiguration());
    }

    /**
     * @test
     */
    public function listReturnsConfigurationForMenuWithSpecialList(): void
    {
        self::assertSame([
            'special' => 'list',
            'special.' => [
                'value' => '1,2',
            ],
        ], MenuConfiguration::list([1, 2])->getTypoScriptConfiguration());

        self::assertSame([
            'special' => 'list',
            'special.' => [
                'value' => '1',
            ],
        ], MenuConfiguration::list([1, '2'])->getTypoScriptConfiguration()); /* @phpstan-ignore-line */
    }

    /**
     * @test
     */
    public function rootlineReturnsConfigurationForMenuWithSpecialRootline(): void
    {
        self::assertSame([
            'special' => 'rootline',
            'special.' => [
                'range' => '1|-1',
            ],
        ], MenuConfiguration::rootline()->getTypoScriptConfiguration());

        self::assertSame([
            'special' => 'rootline',
            'special.' => [
                'range' => '2|-1',
            ],
        ], MenuConfiguration::rootline(2)->getTypoScriptConfiguration());

        self::assertSame([
            'special' => 'rootline',
            'special.' => [
                'range' => '2|-2',
            ],
        ], MenuConfiguration::rootline(2, -2)->getTypoScriptConfiguration());
    }

    /**
     * @test
     */
    public function languageReturnsConfigurationForLanguageMenu(): void
    {
        self::assertSame([
            'languages' => 'auto',
            'addQueryString.' => [
                'exclude' => '',
            ],
        ], MenuConfiguration::language()->getTypoScriptConfiguration());

        self::assertSame([
            'languages' => '1,2',
            'addQueryString.' => [
                'exclude' => '',
            ],
        ], MenuConfiguration::language([1, 2])->getTypoScriptConfiguration());

        self::assertSame([
            'languages' => '1',
            'addQueryString.' => [
                'exclude' => '',
            ],
        ], MenuConfiguration::language([1, '2'])->getTypoScriptConfiguration()); /* @phpstan-ignore-line */

        self::assertSame([
            'languages' => '1,2',
            'addQueryString.' => [
                'exclude' => 'foo,baz',
            ],
        ], MenuConfiguration::language([1, 2], ['foo', 'baz'])->getTypoScriptConfiguration());

        self::assertSame([
            'languages' => '1,2',
            'addQueryString.' => [
                'exclude' => 'foo',
            ],
        ], MenuConfiguration::language([1, 2], ['foo', false])->getTypoScriptConfiguration()); /* @phpstan-ignore-line */
    }

    /**
     * @test
     */
    public function customReturnsConfigurationForCustomMenu(): void
    {
        self::assertSame([
            'dataProcessing.' => [
                '10' => MenuProcessor::class,
                '10.' => [],
            ],
        ], MenuConfiguration::custom(MenuProcessor::class)->getTypoScriptConfiguration());

        self::assertSame([
            'dataProcessing.' => [
                '10' => MenuProcessor::class,
                '10.' => [
                    'foo' => 'baz',
                ],
            ],
        ], MenuConfiguration::custom(MenuProcessor::class, ['foo' => 'baz'])->getTypoScriptConfiguration());
    }

    /**
     * @test
     */
    public function getTypoScriptConfigurationReturnsTypoScriptConfiguration(): void
    {
        $subject = new MenuConfiguration(['foo' => 'baz']);
        $expected = [
            'foo' => 'baz',
        ];

        self::assertSame($expected, $subject->getTypoScriptConfiguration());
    }

    /**
     * @test
     * @dataProvider addTypoScriptConfigurationAddsTypoScriptConfigurationAtGivenPathDataProvider
     * @param string $path
     * @param mixed $value
     * @param array<string, mixed> $expected
     */
    public function addTypoScriptConfigurationAddsTypoScriptConfigurationAtGivenPath(string $path, $value, array $expected): void
    {
        $subject = new MenuConfiguration($this->configuration);
        $subject->addTypoScriptConfiguration($path, $value);

        self::assertSame($expected, $subject->getTypoScriptConfiguration());
    }

    /**
     * @test
     */
    public function getTypeReturnsMenuType(): void
    {
        $subject = new MenuConfiguration([]);

        self::assertSame(MenuConfiguration::DEFAULT, $subject->getType());
    }

    /**
     * @return \Generator<string, array>
     */
    public function addTypoScriptConfigurationAddsTypoScriptConfigurationAtGivenPathDataProvider(): \Generator
    {
        $expectedArray = [
            '10' => 'USER',
            '10.' => [
                'userFunc' => 'foo->baz',
                'userFunc.' => [
                    'foo' => 'baz',
                    'another' => 'foo',
                ],
            ],
        ];

        yield 'array without trailing dot' => ['10.userFunc', ['another' => 'foo'], $expectedArray];
        yield 'array with trailing dot' => ['10.userFunc.', ['another' => 'foo'], $expectedArray];
        yield 'non-array' => ['10.userFunc.another', 'foo', $expectedArray];
    }
}
