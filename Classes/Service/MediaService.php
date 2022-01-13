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
     * @param string $relationFieldName
     * @param string $tableName
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
     * @param int[] $fileReferenceIds
     * @return MediaInterface[]
     */
    public function getFromFileReferences(array $fileReferenceIds): array
    {
        $processorConfiguration = [
            'references' => implode(',', array_filter($fileReferenceIds, 'is_int')),
        ];

        return $this->getFromProcessor($processorConfiguration);
    }

    /**
     * @param int[] $fileIds
     * @return MediaInterface[]
     */
    public function getFromFiles(array $fileIds): array
    {
        $processorConfiguration = [
            'files' => implode(',', array_filter($fileIds, 'is_int')),
        ];

        return $this->getFromProcessor($processorConfiguration);
    }

    /**
     * @param int[] $fileCollectionIds
     * @return MediaInterface[]
     */
    public function getFromFileCollections(array $fileCollectionIds): array
    {
        $processorConfiguration = [
            'collections' => implode(',', array_filter($fileCollectionIds, 'is_int')),
        ];

        return $this->getFromProcessor($processorConfiguration);
    }

    /**
     * @param string[] $folders
     * @return MediaInterface[]
     */
    public function getFromFolders(array $folders): array
    {
        $processorConfiguration = [
            'folders' => implode(',', array_filter($folders, 'is_string')),
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
}
