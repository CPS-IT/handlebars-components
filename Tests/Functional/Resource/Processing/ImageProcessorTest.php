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
use Fr\Typo3HandlebarsComponents\Exception\UnsupportedResourceException;
use Fr\Typo3HandlebarsComponents\Resource\Processing\ImageProcessingInstruction;
use Fr\Typo3HandlebarsComponents\Resource\Processing\ImageProcessor;
use Fr\Typo3HandlebarsComponents\Tests\Functional\FileHandlingTrait;
use Fr\Typo3HandlebarsComponents\Tests\Functional\Fixtures\DummyFile;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * ImageProcessorTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class ImageProcessorTest extends FunctionalTestCase
{
    use FileHandlingTrait;

    /**
     * @var ImageProcessor
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new ImageProcessor();
    }

    /**
     * @test
     */
    public function processThrowsExceptionIfFileOfGivenMediaCannotBeResolved(): void
    {
        $media = new Media(new DummyFile());
        $processingInstruction = new ImageProcessingInstruction($media, 100, 200);

        $this->expectException(UnsupportedResourceException::class);
        $this->expectExceptionCode(1633012917);

        $this->subject->process($media, $processingInstruction);
    }

    /**
     * @test
     */
    public function processUsesOriginalFileForImageProcessing(): void
    {
        $this->file = $this->createDummyFile();

        foreach ([$this->file, $this->createDummyFileReference()] as $file) {
            $media = new Media($file);
            $processingInstruction = new ImageProcessingInstruction($media, 100, 200);

            $actual = $this->subject->process($media, $processingInstruction);

            self::assertSame($this->file, $actual->getOriginalFile());
            self::assertSame(100, $actual->getProperty('width'));
            self::assertSame(200, $actual->getProperty('height'));
        }
    }
}
