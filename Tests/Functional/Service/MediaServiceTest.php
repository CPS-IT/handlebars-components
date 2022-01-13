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

namespace Fr\Typo3HandlebarsComponents\Tests\Functional\Service;

use Fr\Typo3HandlebarsComponents\Domain\Model\Media\Media;
use Fr\Typo3HandlebarsComponents\Resource\Converter\MediaResourceConverter;
use Fr\Typo3HandlebarsComponents\Service\MediaService;
use Fr\Typo3HandlebarsComponents\Tests\Functional\FileHandlingTrait;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\DataProcessing\FilesProcessor;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * MediaServiceTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class MediaServiceTest extends FunctionalTestCase
{
    use FileHandlingTrait;

    /**
     * @var ContentObjectRenderer
     */
    protected $contentObjectRenderer;

    /**
     * @var MediaService
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $this->subject = new MediaService(
            $this->contentObjectRenderer,
            new FilesProcessor(),
            new MediaResourceConverter(GeneralUtility::makeInstance(OnlineMediaHelperRegistry::class))
        );
    }

    /**
     * @test
     */
    public function getFromFileReferencesReturnsMediaFromGivenFileReferenceIds(): void
    {
        $fileReference = $this->createDummyFileReference(false, true)->getUid();

        $actual = $this->subject->getFromFileReferences([$fileReference]);

        self::assertInstanceOf(File::class, $this->file);
        self::assertCount(1, $actual);
        self::assertInstanceOf(Media::class, $actual[0]);
        self::assertInstanceOf(FileReference::class, $actual[0]->getOriginalFile());
        self::assertSame($this->file->getUid(), $actual[0]->getOriginalFile()->getUid());
    }

    /**
     * @test
     */
    public function getFromFilesReturnsMediaFromGivenFileIds(): void
    {
        $this->file = $this->createDummyFile();

        $actual = $this->subject->getFromFiles([$this->file->getUid()]);

        self::assertCount(1, $actual);
        self::assertInstanceOf(Media::class, $actual[0]);
        self::assertInstanceOf(File::class, $actual[0]->getOriginalFile());
        self::assertSame($this->file->getUid(), $actual[0]->getOriginalFile()->getUid());
    }

    /**
     * @test
     */
    public function getFromFoldersReturnsMediaFromGivenFolders(): void
    {
        $this->file = $this->createDummyFile();
        /** @var Folder $folder */
        $folder = $this->file->getParentFolder();

        $actual = $this->subject->getFromFolders([$folder->getCombinedIdentifier()]);

        self::assertCount(1, $actual);
        self::assertInstanceOf(Media::class, $actual[0]);
        self::assertInstanceOf(File::class, $actual[0]->getOriginalFile());
        self::assertSame($this->file->getUid(), $actual[0]->getOriginalFile()->getUid());
    }
}
