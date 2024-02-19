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

namespace Cpsit\Typo3HandlebarsComponents\Tests\Functional\Resource\Processing;

use Cpsit\Typo3HandlebarsComponents\Domain\Model\Media\Media;
use Cpsit\Typo3HandlebarsComponents\Exception\UnsupportedResourceException;
use Cpsit\Typo3HandlebarsComponents\Resource\ImageDimensions;
use Cpsit\Typo3HandlebarsComponents\Resource\Processing\ImageProcessingInstruction;
use Cpsit\Typo3HandlebarsComponents\Resource\Processing\ImageProcessor;
use Cpsit\Typo3HandlebarsComponents\Tests\Functional\FileHandlingTrait;
use Cpsit\Typo3HandlebarsComponents\Tests\Functional\Fixtures\DummyFile;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * ImageProcessorTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class ImageProcessorTest extends FunctionalTestCase
{
    use FileHandlingTrait;

    protected ImageProcessor $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new ImageProcessor();

        // Initialize backend user for TYPO3 < 11
        if ((new Typo3Version())->getMajorVersion() < 11) {
            $this->importCSVDataSet(\dirname(__DIR__, 2) . '/Fixtures/be_users.csv');
            $this->setUpBackendUser(1);
        }
    }

    /**
     * @test
     */
    public function processThrowsExceptionIfFileOfGivenMediaCannotBeResolved(): void
    {
        $media = new Media(new DummyFile());
        $dimensions = ImageDimensions::create()
            ->setWidth(100)
            ->setHeight(200);
        $processingInstruction = new ImageProcessingInstruction($media, $dimensions);

        $this->expectException(UnsupportedResourceException::class);
        $this->expectExceptionCode(1633012917);

        $this->subject->process($media, $processingInstruction);
    }

    /**
     * @test
     * @dataProvider processUsesOriginalFileForImageProcessingDataProvider
     */
    public function processUsesOriginalFileForImageProcessing(
        ImageDimensions $dimensions,
        int $expectedWidth,
        int $expectedHeight
    ): void {
        $this->file = $this->createDummyFile();

        foreach ([$this->file, $this->createDummyFileReference(true)] as $file) {
            $media = new Media($file);
            $processingInstruction = new ImageProcessingInstruction($media, $dimensions);

            $actual = $this->subject->process($media, $processingInstruction);

            self::assertSame($this->file, $actual->getOriginalFile());
            self::assertSame($expectedWidth, $actual->getProperty('width'));
            self::assertSame($expectedHeight, $actual->getProperty('height'));
        }
    }

    /**
     * @return \Generator<string, array{ImageDimensions, int, int}>
     */
    public function processUsesOriginalFileForImageProcessingDataProvider(): \Generator
    {
        yield 'width and height' => [
            ImageDimensions::create()
                ->setWidth(100)
                ->setHeight(200)
            ,
            100,
            200,
        ];
        yield 'no width' => [
            ImageDimensions::create()
                ->setHeight(200)
            ,
            200,
            200,
        ];
        yield 'no height' => [
            ImageDimensions::create()
                ->setWidth(100)
            ,
            100,
            100,
        ];
        yield 'max width only' => [
            ImageDimensions::create()
                ->setMaxWidth(100)
            ,
            100,
            100,
        ];
        yield 'max height only' => [
            ImageDimensions::create()
                ->setMaxHeight(200)
            ,
            200,
            200,
        ];
        yield 'max width and max height' => [
            ImageDimensions::create()
                ->setMaxWidth(100)
                ->setMaxHeight(200)
            ,
            100,
            100,
        ];
    }
}
