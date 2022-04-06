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

namespace Fr\Typo3HandlebarsComponents\Tests\Unit\Exception;

use Fr\Typo3HandlebarsComponents\Exception\InvalidImageDimensionException;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * InvalidImageDimensionExceptionTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class InvalidImageDimensionExceptionTest extends UnitTestCase
{
    /**
     * @test
     */
    public function createReturnsExceptionForGivenDimension(): void
    {
        $actual = InvalidImageDimensionException::create(false);

        self::assertInstanceOf(InvalidImageDimensionException::class, $actual);
        self::assertSame(1631807380, $actual->getCode());
        self::assertSame('Image dimensions must be of type integer or string, bool given.', $actual->getMessage());
    }

    /**
     * @test
     */
    public function forMissingDimensionsReturnsExceptionForMissingDimensions(): void
    {
        $actual = InvalidImageDimensionException::forMissingDimensions();

        self::assertInstanceOf(InvalidImageDimensionException::class, $actual);
        self::assertSame(1649237990, $actual->getCode());
        self::assertSame('No image dimensions defined. You must define at least one image dimension, e.g. width or height.', $actual->getMessage());
    }

    /**
     * @test
     */
    public function forUnresolvableDimensionReturnsExceptionForUnresolvableDimension(): void
    {
        $actual = InvalidImageDimensionException::forUnresolvableDimension('foo');

        self::assertInstanceOf(InvalidImageDimensionException::class, $actual);
        self::assertSame(1631807435, $actual->getCode());
        self::assertSame('Image sizes must be integers, optionally followed by "c" or "m", "foo" given.', $actual->getMessage());
    }
}
