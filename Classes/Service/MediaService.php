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

namespace Fr\Typo3HandlebarsComponents\Service;

use Fr\Typo3HandlebarsComponents\Domain\Model\Media\MediaInterface;
use Fr\Typo3HandlebarsComponents\Resource\Converter\ResourceConverterInterface;
use TYPO3\CMS\Core\Resource\Collection\AbstractFileCollection;
use TYPO3\CMS\Core\Resource\File as CoreFile;
use TYPO3\CMS\Core\Resource\FileReference as CoreFileReference;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Extbase\Domain\Model\File as ExtbaseFile;
use TYPO3\CMS\Extbase\Domain\Model\FileReference as ExtbaseFileReference;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\DataProcessing\FilesProcessor;

/**
 * MediaService
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class MediaService
{
    /**
     * @var ContentObjectRenderer
     */
    protected $contentObjectRenderer;

    /**
     * @var FilesProcessor
     */
    protected $processor;

    /**
     * @var ResourceConverterInterface
     */
    protected $converter;

    public function __construct(
        ContentObjectRenderer $contentObjectRenderer,
        FilesProcessor $processor,
        ResourceConverterInterface $converter
    ) {
        $this->contentObjectRenderer = $contentObjectRenderer;
        $this->processor = $processor;
        $this->converter = $converter;
    }

    /**
     * @param array<string, mixed> $record
     * @return MediaInterface[]
     */
    public function getFromRelation(string $relationFieldName, string $tableName, array $record): array
    {
        $this->contentObjectRenderer->start($record, $tableName);

        $processorConfiguration = [
            'references.' => [
                'fieldName' => $relationFieldName,
                'table' => $tableName,
            ],
        ];

        return $this->getFromProcessor($processorConfiguration);
    }

    /**
     * @param list<int|CoreFileReference|ExtbaseFileReference> $fileReferences
     * @return list<MediaInterface>
     */
    public function getFromFileReferences(array $fileReferences): array
    {
        $processorConfiguration = [
            'references' => implode(',', array_filter(array_map([$this, 'resolveFileReference'], $fileReferences))),
        ];

        return $this->getFromProcessor($processorConfiguration);
    }

    /**
     * @param list<int|CoreFile|ExtbaseFile> $files
     * @return list<MediaInterface>
     */
    public function getFromFiles(array $files): array
    {
        $processorConfiguration = [
            'files' => implode(',', array_filter(array_map([$this, 'resolveFile'], $files))),
        ];

        return $this->getFromProcessor($processorConfiguration);
    }

    /**
     * @param list<int|AbstractFileCollection> $fileCollections
     * @return list<MediaInterface>
     */
    public function getFromFileCollections(array $fileCollections): array
    {
        $processorConfiguration = [
            'collections' => implode(',', array_filter(array_map([$this, 'resolveFileCollection'], $fileCollections))),
        ];

        return $this->getFromProcessor($processorConfiguration);
    }

    /**
     * @param list<string|Folder> $folders
     * @return list<MediaInterface>
     */
    public function getFromFolders(array $folders): array
    {
        $processorConfiguration = [
            'folders' => implode(',', array_filter(array_map([$this, 'resolveFolder'], $folders))),
        ];

        return $this->getFromProcessor($processorConfiguration);
    }

    /**
     * @param array<string, mixed> $processorConfiguration
     * @return MediaInterface[]
     */
    protected function getFromProcessor(array $processorConfiguration): array
    {
        // Collect files from processor
        $processorConfiguration['as'] = 'files';
        ['files' => $files] = $this->processor->process($this->contentObjectRenderer, [], $processorConfiguration, []);
        $media = [];

        // Convert files to known objects
        foreach ($files as $file) {
            $media[] = $this->converter->convert($file);
        }

        return $media;
    }

    /**
     * @param int|CoreFileReference|ExtbaseFileReference $fileReference
     */
    protected function resolveFileReference($fileReference): ?int
    {
        if ($fileReference instanceof ExtbaseFileReference) {
            $fileReference = $fileReference->getOriginalResource();
        }
        if ($fileReference instanceof CoreFileReference) {
            $fileReference = $fileReference->getUid();
        }

        if (\is_int($fileReference)) {
            return $fileReference;
        }

        /* @phpstan-ignore-next-line */
        return null;
    }

    /**
     * @param int|CoreFile|ExtbaseFile $file
     */
    protected function resolveFile($file): ?int
    {
        if ($file instanceof ExtbaseFile) {
            $file = $file->getOriginalResource();
        }
        if ($file instanceof CoreFile) {
            $file = $file->getUid();
        }

        if (\is_int($file)) {
            return $file;
        }

        /* @phpstan-ignore-next-line */
        return null;
    }

    /**
     * @param int|AbstractFileCollection $fileCollection
     */
    protected function resolveFileCollection($fileCollection): ?int
    {
        if ($fileCollection instanceof AbstractFileCollection) {
            $fileCollection = $fileCollection->getUid();
        }

        if (\is_int($fileCollection)) {
            return $fileCollection;
        }

        /* @phpstan-ignore-next-line */
        return null;
    }

    /**
     * @param string|Folder $folder
     */
    protected function resolveFolder($folder): ?string
    {
        if ($folder instanceof Folder) {
            $folder = $folder->getCombinedIdentifier();
        }

        if (\is_string($folder)) {
            return $folder;
        }

        /* @phpstan-ignore-next-line */
        return null;
    }
}
