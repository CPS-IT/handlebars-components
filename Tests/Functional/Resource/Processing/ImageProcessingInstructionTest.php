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
use Fr\Typo3HandlebarsComponents\Resource\Processing\ImageProcessingInstruction;
use Fr\Typo3HandlebarsComponents\Tests\Functional\FileHandlingTrait;
use TYPO3\CMS\Core\Imaging\ImageManipulation\Area;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * ImageProcessingInstructionTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class ImageProcessingInstructionTest extends FunctionalTestCase
{
    use FileHandlingTrait;

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
        $this->media = new Media($this->createDummyFile());
        $this->subject = new ImageProcessingInstruction($this->media, '100c', '200m');
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
        $subject = new ImageProcessingInstruction(new Media($fileReference), 100, 200);

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
        $subject = new ImageProcessingInstruction(new Media($fileReference), 100, 200);

        self::assertNull($subject->parse()['crop']);
    }
}
