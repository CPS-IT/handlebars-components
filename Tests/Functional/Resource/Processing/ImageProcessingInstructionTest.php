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

namespace Fr\Typo3HandlebarsComponents\Tests\Functional\Resource\Processing;

use Fr\Typo3HandlebarsComponents\Domain\Model\Media\Media;
use Fr\Typo3HandlebarsComponents\Resource\ImageDimensions;
use Fr\Typo3HandlebarsComponents\Resource\Processing\ImageProcessingInstruction;
use Fr\Typo3HandlebarsComponents\Tests\Functional\FileHandlingTrait;
use TYPO3\CMS\Core\Imaging\ImageManipulation\Area;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * ImageProcessingInstructionTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class ImageProcessingInstructionTest extends FunctionalTestCase
{
    use FileHandlingTrait;

    protected Media $media;
    protected ImageDimensions $dimensions;
    protected ImageProcessingInstruction $subject;

    protected function setUp(): void
    {
        parent::setUp();

        // Initialize backend user for TYPO3 < 11
        if ((new Typo3Version())->getMajorVersion() < 11) {
            $this->importCSVDataSet(\dirname(__DIR__, 2) . '/Fixtures/be_users.csv');
            $this->setUpBackendUser(1);
        }

        $this->media = new Media($this->createDummyFile());
        $this->dimensions = ImageDimensions::create()
            ->setWidth('100c')
            ->setHeight('200m');
        $this->subject = new ImageProcessingInstruction($this->media, $this->dimensions);
    }

    /**
     * @test
     */
    public function parseReturnsParsedProcessingInstructions(): void
    {
        $actual = $this->subject->parse();

        self::assertSame('100c', $actual['width']);
        self::assertSame('200m', $actual['height']);
        self::assertNull($actual['crop']);
    }

    /**
     * @test
     */
    public function parseReturnsProcessingInstructionsWithParsedCropArea(): void
    {
        $fileReference = $this->createDummyFileReference();
        $dimensions = ImageDimensions::create()
            ->setWidth(100)
            ->setHeight(200);
        $subject = new ImageProcessingInstruction(new Media($fileReference), $dimensions);

        /** @var Area $actual */
        $actual = $subject->parse()['crop'];

        self::assertInstanceOf(Area::class, $actual);
        self::assertNotSame(1.0, $actual->getWidth());
        self::assertNotSame(1.0, $actual->getHeight());
        self::assertFalse($actual->isEmpty());
    }

    /**
     * @test
     */
    public function parseReturnsProcessingInstructionsWithEmptyCropArea(): void
    {
        $fileReference = $this->createDummyFileReference(true);
        $dimensions = ImageDimensions::create()
            ->setWidth(100)
            ->setHeight(200);
        $subject = new ImageProcessingInstruction(new Media($fileReference), $dimensions);

        self::assertNull($subject->parse()['crop']);
    }
}
