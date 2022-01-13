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

namespace Fr\Typo3HandlebarsComponents\Tests\Unit\Renderer\Component\Layout;

use Fr\Typo3HandlebarsComponents\Exception\UnsupportedTypeException;
use Fr\Typo3HandlebarsComponents\Renderer\Component\Layout\HandlebarsLayoutAction;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * HandlebarsLayoutActionTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class HandlebarsLayoutActionTest extends UnitTestCase
{
    /**
     * @test
     */
    public function constructorThrowsExceptionIfInvalidModeIsGiven(): void
    {
        $this->expectException(UnsupportedTypeException::class);
        $this->expectExceptionCode(1630333499);
        $this->expectExceptionMessage('The type "foo" is not supported.');

        new HandlebarsLayoutAction([], 'trim', 'foo');
    }

    /**
     * @test
     * @dataProvider renderReturnsRenderedAndProcessedValueDataProvider
     * @param string $mode
     * @param string $expected
     */
    public function renderReturnsRenderedAndProcessedValue(string $mode, string $expected): void
    {
        /**
         * @param array<string, string> $data
         * @return string
         */
        $function = function (array $data): string {
            return $data['foo'];
        };
        $subject = new HandlebarsLayoutAction(['foo' => 'baz'], $function, $mode);

        self::assertSame($expected, $subject->render('foo'));
    }

    /**
     * @return \Generator<string, array{string, string}>
     */
    public function renderReturnsRenderedAndProcessedValueDataProvider(): \Generator
    {
        yield 'mode=replace' => [HandlebarsLayoutAction::REPLACE, 'baz'];
        yield 'mode=append' => [HandlebarsLayoutAction::APPEND, 'foobaz'];
        yield 'mode=prepend' => [HandlebarsLayoutAction::PREPEND, 'bazfoo'];
    }
}
