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
use TYPO3\CMS\Core\Information\Typo3Version;
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
final class MediaServiceTest extends FunctionalTestCase
{
    use FileHandlingTrait;

    private const TEST_MODE_OBJECT = 0;
    private const TEST_MODE_IDENTIFIER = 1;

    protected ContentObjectRenderer $contentObjectRenderer;
    protected MediaService $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $this->subject = new MediaService(
            $this->contentObjectRenderer,
            new FilesProcessor(),
            new MediaResourceConverter(GeneralUtility::makeInstance(OnlineMediaHelperRegistry::class))
        );

        // Initialize backend user for TYPO3 < 11
        if ((new Typo3Version())->getMajorVersion() < 11) {
            $this->importCSVDataSet(\dirname(__DIR__) . '/Fixtures/be_users.csv');
            $this->setUpBackendUser(1);
        }
    }

    /**
     * @test
     * @dataProvider getFromFileReferencesReturnsMediaFromGivenFileReferencesDataProvider
     */
    public function getFromFileReferencesReturnsMediaFromGivenFileReferences(int $testMode): void
    {
        $fileReference = $this->createDummyFileReference(false, true);

        switch ($testMode) {
            case self::TEST_MODE_OBJECT:
                break;

            case self::TEST_MODE_IDENTIFIER:
                $fileReference = $fileReference->getUid();
                break;

            default:
                throw new \UnexpectedValueException(sprintf('The given test mode "%d" is invalid.', $testMode), 1647612628);
        }

        $actual = $this->subject->getFromFileReferences([$fileReference]);

        self::assertInstanceOf(File::class, $this->file);
        self::assertCount(1, $actual);
        self::assertInstanceOf(Media::class, $actual[0]);
        self::assertInstanceOf(FileReference::class, $actual[0]->getOriginalFile());
        self::assertSame($this->file->getUid(), $actual[0]->getOriginalFile()->getUid());
    }

    /**
     * @test
     * @dataProvider getFromFilesReturnsMediaFromGivenFilesDataProvider
     */
    public function getFromFilesReturnsMediaFromGivenFiles(int $testMode): void
    {
        $this->file = $this->createDummyFile();

        switch ($testMode) {
            case self::TEST_MODE_OBJECT:
                $file = $this->file;
                break;

            case self::TEST_MODE_IDENTIFIER:
                $file = $this->file->getUid();
                break;

            default:
                throw new \UnexpectedValueException(sprintf('The given test mode "%d" is invalid.', $testMode), 1647612806);
        }

        $actual = $this->subject->getFromFiles([$file]);

        self::assertCount(1, $actual);
        self::assertInstanceOf(Media::class, $actual[0]);
        self::assertInstanceOf(File::class, $actual[0]->getOriginalFile());
        self::assertSame($this->file->getUid(), $actual[0]->getOriginalFile()->getUid());
    }

    /**
     * @test
     * @dataProvider getFromFoldersReturnsMediaFromGivenFoldersDataProvider
     */
    public function getFromFoldersReturnsMediaFromGivenFolders(int $testMode): void
    {
        $this->file = $this->createDummyFile();
        /** @var Folder $folder */
        $folder = $this->file->getParentFolder();

        switch ($testMode) {
            case self::TEST_MODE_OBJECT:
                break;

            case self::TEST_MODE_IDENTIFIER:
                $folder = $folder->getCombinedIdentifier();
                break;

            default:
                throw new \UnexpectedValueException(sprintf('The given test mode "%d" is invalid.', $testMode), 1647613011);
        }

        $actual = $this->subject->getFromFolders([$folder]);

        self::assertCount(1, $actual);
        self::assertInstanceOf(Media::class, $actual[0]);
        self::assertInstanceOf(File::class, $actual[0]->getOriginalFile());
        self::assertSame($this->file->getUid(), $actual[0]->getOriginalFile()->getUid());
    }

    /**
     * @return \Generator<string, array{int}>
     */
    public function getFromFileReferencesReturnsMediaFromGivenFileReferencesDataProvider(): \Generator
    {
        yield 'file reference object' => [self::TEST_MODE_OBJECT];
        yield 'file reference identifier' => [self::TEST_MODE_IDENTIFIER];
    }

    /**
     * @return \Generator<string, array{int}>
     */
    public function getFromFilesReturnsMediaFromGivenFilesDataProvider(): \Generator
    {
        yield 'file object' => [self::TEST_MODE_OBJECT];
        yield 'file identifier' => [self::TEST_MODE_IDENTIFIER];
    }

    /**
     * @return \Generator<string, array{int}>
     */
    public function getFromFoldersReturnsMediaFromGivenFoldersDataProvider(): \Generator
    {
        yield 'folder object' => [self::TEST_MODE_OBJECT];
        yield 'folder identifier' => [self::TEST_MODE_IDENTIFIER];
    }
}
