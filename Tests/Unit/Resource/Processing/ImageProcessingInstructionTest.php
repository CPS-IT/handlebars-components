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

namespace Fr\Typo3HandlebarsComponents\Tests\Unit\Resource\Processing;

use Fr\Typo3HandlebarsComponents\Domain\Model\Media\Media;
use Fr\Typo3HandlebarsComponents\Exception\InvalidImageDimensionException;
use Fr\Typo3HandlebarsComponents\Resource\ImageDimensions;
use Fr\Typo3HandlebarsComponents\Resource\Processing\ImageProcessingInstruction;
use Fr\Typo3HandlebarsComponents\Tests\Unit\Fixtures\DummyResourceStorage;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * ImageProcessingInstructionTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class ImageProcessingInstructionTest extends UnitTestCase
{
    protected Media $media;
    protected ImageDimensions $dimensions;
    protected ImageProcessingInstruction $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->media = new Media(new File([], new DummyResourceStorage()));
        $this->dimensions = ImageDimensions::create()
            ->setWidth('100c')
            ->setHeight('200m');
        $this->subject = new ImageProcessingInstruction($this->media, $this->dimensions);
    }

    /**
     * @test
     */
    public function constructorThrowsExceptionIfNoImageDimensionsAreGiven(): void
    {
        $this->expectException(InvalidImageDimensionException::class);
        $this->expectExceptionCode(1649237990);
        $this->expectExceptionMessage('No image dimensions defined. You must define at least one image dimension, e.g. width or height.');

        new ImageProcessingInstruction($this->media, ImageDimensions::create());
    }

    /**
     * @test
     */
    public function getMediaReturnsMedia(): void
    {
        self::assertSame($this->media, $this->subject->getMedia());
    }

    /**
     * @test
     */
    public function getWidthReturnsWidth(): void
    {
        self::assertSame('100c', $this->subject->getWidth());
    }

    /**
     * @test
     */
    public function getWidthReturnsNullIfWidthIsNull(): void
    {
        $this->dimensions->setWidth(null);

        self::assertNull($this->subject->getWidth());
    }

    /**
     * @test
     */
    public function getNormalizedWidthReturnsParsedWidth(): void
    {
        self::assertSame(100, $this->subject->getNormalizedWidth());
    }

    /**
     * @test
     */
    public function getNormalizedWidthReturnsNullIfWidthIsNull(): void
    {
        $this->dimensions->setWidth(null);

        self::assertNull($this->subject->getNormalizedWidth());
    }

    /**
     * @test
     */
    public function getHeightReturnsHeight(): void
    {
        self::assertSame('200m', $this->subject->getHeight());
    }

    /**
     * @test
     */
    public function getHeightReturnsNullIfHeightIsNull(): void
    {
        $this->dimensions->setHeight(null);

        self::assertNull($this->subject->getHeight());
    }

    /**
     * @test
     */
    public function getNormalizedHeightReturnsParsedHeight(): void
    {
        self::assertSame(200, $this->subject->getNormalizedHeight());
    }

    /**
     * @test
     */
    public function getNormalizedHeightReturnsNullIfHeightIsNull(): void
    {
        $this->dimensions->setHeight(null);

        self::assertNull($this->subject->getNormalizedHeight());
    }

    /**
     * @test
     */
    public function getMaxWidthReturnsMaxWidth(): void
    {
        self::assertNull($this->subject->getMaxWidth());

        $this->dimensions->setMaxWidth(100);

        self::assertSame(100, $this->subject->getMaxWidth());
    }

    /**
     * @test
     */
    public function getMaxHeightReturnsMaxHeight(): void
    {
        self::assertNull($this->subject->getMaxHeight());

        $this->dimensions->setMaxHeight(100);

        self::assertSame(100, $this->subject->getMaxHeight());
    }

    /**
     * @test
     */
    public function getDimensionsReturnsImageDimensions(): void
    {
        self::assertSame($this->dimensions, $this->subject->getDimensions());
    }

    /**
     * @test
     */
    public function getTypeReturnsType(): void
    {
        self::assertSame(ImageProcessingInstruction::DEFAULT, $this->subject->getType());
    }

    /**
     * @test
     */
    public function isDefaultTestsWhetherTypeIsDefault(): void
    {
        self::assertTrue($this->subject->isDefault());

        $subject = new ImageProcessingInstruction($this->media, $this->dimensions, ImageProcessingInstruction::SOURCE);

        self::assertFalse($subject->isDefault());
    }

    /**
     * @test
     * @dataProvider isSourceTestsWhetherTypeIsDefaultDataProvider
     */
    public function isSourceTestsWhetherTypeIsDefault(string $source, bool $expected): void
    {
        $subject = new ImageProcessingInstruction($this->media, $this->dimensions, $source);

        self::assertSame($expected, $subject->isSource());
    }

    /**
     * @test
     */
    public function getMediaQueryReturnsMediaQuery(): void
    {
        self::assertNull($this->subject->getMediaQuery());
        self::assertSame('(min-width: 100px)', $this->subject->setMediaQuery('(min-width: 100px)')->getMediaQuery());
    }

    /**
     * @test
     */
    public function getCropVariantReturnsCropVariant(): void
    {
        self::assertSame('default', $this->subject->getCropVariant());
        self::assertSame('foo', $this->subject->setCropVariant('foo')->getCropVariant());
    }

    /**
     * @return \Generator<string, array{string, bool}>
     */
    public function isSourceTestsWhetherTypeIsDefaultDataProvider(): \Generator
    {
        yield 'default type' => [ImageProcessingInstruction::DEFAULT, false];
        yield 'source type' => [ImageProcessingInstruction::SOURCE, true];
        yield 'source S' => ['sourceS', true];
        yield 'source M' => ['sourceM', true];
        yield 'source L' => ['sourceL', true];
        yield 'source XL' => ['sourceXL', true];
    }
}
