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
class ImageProcessingInstructionTest extends UnitTestCase
{
    /**
     * @var Media
     */
    protected $media;

    /**
     * @var ImageProcessingInstruction
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->media = new Media(new File([], new DummyResourceStorage()));
        $this->subject = new ImageProcessingInstruction($this->media, '100c', '200m');
    }

    /**
     * @test
     */
    public function constructorThrowsExceptionIfNoImageDimensionsAreGiven(): void
    {
        $this->expectException(InvalidImageDimensionException::class);
        $this->expectExceptionCode(1649237990);
        $this->expectExceptionMessage('No image dimensions defined. You must define at least one image dimension, e.g. width or height.');

        new ImageProcessingInstruction($this->media);
    }

    /**
     * @test
     */
    public function constructorThrowsExceptionIfTypeOfGivenSizeIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1631807380);

        /* @phpstan-ignore-next-line */
        new ImageProcessingInstruction($this->media, false, 200);
    }

    /**
     * @test
     */
    public function constructorThrowsExceptionIfGivenSizeContainsInvalidConfigurationValues(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1631807435);

        new ImageProcessingInstruction($this->media, '100x', 200);
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
        $subject = new ImageProcessingInstruction($this->media, null, 100);

        self::assertNull($subject->getWidth());
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
        $subject = new ImageProcessingInstruction($this->media, null, 100);

        self::assertNull($subject->getNormalizedWidth());
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
        $subject = new ImageProcessingInstruction($this->media, 100);

        self::assertNull($subject->getHeight());
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
        $subject = new ImageProcessingInstruction($this->media, 100);

        self::assertNull($subject->getNormalizedHeight());
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

        $subject = new ImageProcessingInstruction($this->media, 100, 200, ImageProcessingInstruction::SOURCE);

        self::assertFalse($subject->isDefault());
    }

    /**
     * @test
     * @dataProvider isSourceTestsWhetherTypeIsDefaultDataProvider
     * @param string $source
     * @param bool $expected
     */
    public function isSourceTestsWhetherTypeIsDefault(string $source, bool $expected): void
    {
        $subject = new ImageProcessingInstruction($this->media, 100, 200, $source);

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
