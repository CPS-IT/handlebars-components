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

namespace Fr\Typo3HandlebarsComponents\Tests\Functional\Resource\Converter;

use Fr\Typo3HandlebarsComponents\Domain\Model\Media\Media;
use Fr\Typo3HandlebarsComponents\Domain\Model\Media\OnlineMedia;
use Fr\Typo3HandlebarsComponents\Exception\UnsupportedResourceException;
use Fr\Typo3HandlebarsComponents\Resource\Converter\MediaResourceConverter;
use Fr\Typo3HandlebarsComponents\Tests\Functional\FileHandlingTrait;
use Fr\Typo3HandlebarsComponents\Tests\Functional\Fixtures\DummyFile;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * MediaResourceConverterTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class MediaResourceConverterTest extends FunctionalTestCase
{
    use FileHandlingTrait;

    /**
     * @var OnlineMediaHelperRegistry
     */
    protected $onlineMediaHelperRegistry;

    /**
     * @var MediaResourceConverter
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->onlineMediaHelperRegistry = GeneralUtility::makeInstance(OnlineMediaHelperRegistry::class);
        $this->subject = new MediaResourceConverter($this->onlineMediaHelperRegistry);
    }

    /**
     * @test
     */
    public function convertThrowsExceptionIfGivenFileIsUnsupported(): void
    {
        $this->expectException(UnsupportedResourceException::class);
        $this->expectExceptionCode(1633012917);

        $this->subject->convert(new DummyFile('foo.youtube'));
    }

    /**
     * @test
     */
    public function convertConvertsGivenFileToMediaObject(): void
    {
        $this->file = $this->createDummyFile();

        $actual = $this->subject->convert($this->file);

        self::assertInstanceOf(Media::class, $actual);
        self::assertSame($this->file, $actual->getOriginalFile());
        self::assertSame('dummy', $actual->getName());
        self::assertSame('png', $actual->getExtension());
    }

    /**
     * @test
     */
    public function convertConvertsGivenFileToOnlineMediaObject(): void
    {
        $videoUrl = 'https://www.youtube.com/watch?v=IkdmOVejUlI';
        $targetFolder = $this->getResourceFactory()->getDefaultStorage()->getDefaultFolder();
        $this->file = $this->onlineMediaHelperRegistry->transformUrlToFile($videoUrl, $targetFolder);

        foreach ([$this->file, $this->createDummyFileReference()] as $file) {
            $actual = $this->subject->convert($file);

            self::assertInstanceOf(OnlineMedia::class, $actual);
            self::assertSame($file, $actual->getOriginalFile());
            self::assertSame('IkdmOVejUlI', $actual->getOnlineMediaId());
            self::assertSame($videoUrl, $actual->getPublicUrl());
        }
    }
}
