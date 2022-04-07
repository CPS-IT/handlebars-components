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

namespace Fr\Typo3HandlebarsComponents\Tests\Unit\Resource;

use Fr\Typo3HandlebarsComponents\Exception\InvalidImageDimensionException;
use Fr\Typo3HandlebarsComponents\Resource\ImageDimensions;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * ImageDimensionsTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class ImageDimensionsTest extends UnitTestCase
{
    /**
     * @var ImageDimensions
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = ImageDimensions::create()
            ->setWidth(100)
            ->setHeight(200);
    }

    /**
     * @test
     */
    public function constructorSetsWidthAndHeight(): void
    {
        $actual = new ImageDimensions(100, 200);

        self::assertSame(100, $actual->getWidth());
        self::assertSame(200, $actual->getHeight());
    }

    /**
     * @test
     */
    public function getWidthReturnsWidth(): void
    {
        self::assertSame(100, $this->subject->getWidth());
    }

    /**
     * @test
     */
    public function setWidthThrowsExceptionIfTypeOfGivenWidthIsInvalid(): void
    {
        $this->expectException(InvalidImageDimensionException::class);
        $this->expectExceptionCode(1631807380);
        $this->expectExceptionMessage('Image dimensions must be of type integer or string, bool given.');

        /* @phpstan-ignore-next-line */
        $this->subject->setWidth(false);
    }

    /**
     * @test
     */
    public function setWidthThrowsExceptionIfGivenWidthContainsInvalidConfigurationValues(): void
    {
        $this->expectException(InvalidImageDimensionException::class);
        $this->expectExceptionCode(1631807435);
        $this->expectExceptionMessage('Image sizes must be integers, optionally followed by "c" or "m", "100x" given.');

        $this->subject->setWidth('100x');
    }

    /**
     * @test
     */
    public function setWidthAppliesGivenWidth(): void
    {
        self::assertSame(150, $this->subject->setWidth(150)->getWidth());
    }

    /**
     * @test
     */
    public function getHeightReturnsHeight(): void
    {
        self::assertSame(200, $this->subject->getHeight());
    }

    /**
     * @test
     */
    public function setHeightAppliesGivenHeight(): void
    {
        self::assertSame(150, $this->subject->setHeight(150)->getHeight());
    }

    /**
     * @test
     */
    public function setHeightThrowsExceptionIfTypeOfGivenHeightIsInvalid(): void
    {
        $this->expectException(InvalidImageDimensionException::class);
        $this->expectExceptionCode(1631807380);
        $this->expectExceptionMessage('Image dimensions must be of type integer or string, bool given.');

        /* @phpstan-ignore-next-line */
        $this->subject->setHeight(false);
    }

    /**
     * @test
     */
    public function setHeightThrowsExceptionIfGivenHeightContainsInvalidConfigurationValues(): void
    {
        $this->expectException(InvalidImageDimensionException::class);
        $this->expectExceptionCode(1631807435);
        $this->expectExceptionMessage('Image sizes must be integers, optionally followed by "c" or "m", "100x" given.');

        $this->subject->setHeight('100x');
    }

    /**
     * @test
     */
    public function getMaxWidthReturnsMaxWidth(): void
    {
        self::assertNull($this->subject->getMaxWidth());
    }

    /**
     * @test
     */
    public function setMaxWidthAppliesGivenMaxWidth(): void
    {
        self::assertSame(150, $this->subject->setMaxWidth(150)->getMaxWidth());
    }

    /**
     * @test
     */
    public function getMaxHeightReturnsMaxHeight(): void
    {
        self::assertNull($this->subject->getMaxHeight());
    }

    /**
     * @test
     */
    public function setMaxHeightAppliesGivenMaxHeight(): void
    {
        self::assertSame(150, $this->subject->setMaxHeight(150)->getMaxHeight());
    }
}
