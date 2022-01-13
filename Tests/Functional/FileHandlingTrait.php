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

namespace Fr\Typo3HandlebarsComponents\Tests\Functional;

use TYPO3\CMS\Core\Resource\DuplicationBehavior;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FileReference as ExtbaseFileReference;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

/**
 * FileHandlingTrait
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
trait FileHandlingTrait
{
    /**
     * @var ResourceFactory
     */
    protected $resourceFactory;

    /**
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager;

    /**
     * @var File|null
     */
    protected $file;

    protected function createDummyFile(): File
    {
        $storage = $this->getResourceFactory()->getDefaultStorage();
        $testFile = __DIR__ . '/Fixtures/dummy.png';

        self::assertInstanceOf(ResourceStorage::class, $storage);

        /** @var File $file */
        $file = $storage->addFile(
            $testFile,
            $storage->getDefaultFolder(),
            basename($testFile),
            DuplicationBehavior::REPLACE,
            false
        );

        return $this->file = $file;
    }

    protected function createDummyFileReference(bool $useEmptyCropping = false, bool $persist = false): FileReference
    {
        $file = $this->file ?? $this->file = $this->createDummyFile();

        if ($useEmptyCropping) {
            $crop = '{"default":{"cropArea":{"height":1,"width":1,"x":0,"y":0},"selectedRatio":"NaN","focusArea":null}}';
        } else {
            $crop = '{"default":{"cropArea":{"height":0.1525,"width":0.3425,"x":0.2775,"y":0.4425},"selectedRatio":"NaN","focusArea":null}}';
        }

        $fileReference = $this->getResourceFactory()->createFileReferenceObject([
            'uid_local' => $file->getUid(),
            'uid_foreign' => 1,
            'tablenames' => 'pages',
            'crop' => $crop,
        ]);

        if (!$persist) {
            return $fileReference;
        }

        $extbaseFileReference = GeneralUtility::makeInstance(ExtbaseFileReference::class);
        $extbaseFileReference->setOriginalResource($fileReference);
        $this->getPersistenceManager()->add($extbaseFileReference);
        $this->getPersistenceManager()->persistAll();

        self::assertIsInt($extbaseFileReference->getUid());

        return $this->getResourceFactory()->getFileReferenceObject($extbaseFileReference->getUid());
    }

    protected function getResourceFactory(): ResourceFactory
    {
        if (null === $this->resourceFactory) {
            $this->resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
        }

        return $this->resourceFactory;
    }

    protected function getPersistenceManager(): PersistenceManagerInterface
    {
        if (null === $this->persistenceManager) {
            $this->persistenceManager = GeneralUtility::makeInstance(PersistenceManagerInterface::class);
        }

        return $this->persistenceManager;
    }
}
