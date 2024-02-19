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

namespace Cpsit\Typo3HandlebarsComponents\Tests\Unit\Utility;

use Cpsit\Typo3HandlebarsComponents\Exception\InvalidConfigurationException;
use Cpsit\Typo3HandlebarsComponents\Utility\TypoScriptUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * TypoScriptUtilityTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class TypoScriptUtilityTest extends UnitTestCase
{
    /**
     * @test
     * @dataProvider transformArrayPathToTypoScriptArrayPathSplitsGivenPathIntoTypoScriptArrayPathSegmentsDataProvider
     * @param string[] $expected
     */
    public function transformArrayPathToTypoScriptArrayPathSplitsGivenPathIntoTypoScriptArrayPathSegments(
        string $path,
        array $expected
    ): void {
        self::assertSame($expected, TypoScriptUtility::transformArrayPathToTypoScriptArrayPath($path));
    }

    /**
     * @test
     */
    public function buildTypoScriptArrayFromPathCreatesTypoScriptArrayFromGivenPath(): void
    {
        $expected = [
            'foo.' => [
                'baz.' => [
                    'another' => 'foo',
                ],
            ],
        ];

        self::assertSame($expected, TypoScriptUtility::buildTypoScriptArrayFromPath('foo.baz.another', 'foo'));
    }

    /**
     * @test
     * @dataProvider validateTypoScriptArrayValidatesGivenTypoScriptArrayDataProvider
     * @param array<string, mixed> $typoScriptArray
     */
    public function validateTypoScriptArrayValidatesGivenTypoScriptArray(array $typoScriptArray, string $expectedExceptionPath = null): void
    {
        if ($expectedExceptionPath !== null) {
            $this->expectException(InvalidConfigurationException::class);
            $this->expectExceptionCode(1631118231);
            $this->expectExceptionMessage(sprintf('The configuration for path "%s" is missing or invalid.', $expectedExceptionPath));
        }

        TypoScriptUtility::validateTypoScriptArray($typoScriptArray);
    }

    /**
     * @return \Generator<string, array{string, array<string>}>
     */
    public function transformArrayPathToTypoScriptArrayPathSplitsGivenPathIntoTypoScriptArrayPathSegmentsDataProvider(): \Generator
    {
        yield 'empty path' => ['', []];
        yield 'single-depth non-array path' => ['foo', ['foo']];
        yield 'single-depth array path' => ['foo.', ['foo.']];
        yield 'chained non-array path' => ['foo.baz', ['foo.', 'baz']];
        yield 'chained array path' => ['foo.baz.', ['foo.', 'baz.']];
    }

    /**
     * @return \Generator<string, array{0: array<int|string, string|array<string, string>>, 1?: string}>
     */
    public function validateTypoScriptArrayValidatesGivenTypoScriptArrayDataProvider(): \Generator
    {
        $validArray = [
            '10' => 'USER',
            '10.' => [
                'userFunc' => 'foo->baz',
            ],
        ];
        $invalidArray1 = [
            '10' => [
                'userFunc' => 'foo->baz',
            ],
        ];
        $invalidArray2 = [
            '10.' => [
                'userFunc.' => 'foo->baz',
            ],
        ];

        yield 'valid array' => [$validArray];
        yield 'invalid array without dot-notation for array' => [$invalidArray1, '10'];
        yield 'invalid array with dot-notation for non-array' => [$invalidArray2, '10.userFunc.'];
    }
}
