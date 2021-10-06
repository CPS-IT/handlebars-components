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

namespace Fr\Typo3HandlebarsComponents\Resource\Converter;

use Fr\Typo3HandlebarsComponents\Domain\Model\Media\Media;
use Fr\Typo3HandlebarsComponents\Domain\Model\Media\MediaInterface;
use Fr\Typo3HandlebarsComponents\Domain\Model\Media\OnlineMedia;
use Fr\Typo3HandlebarsComponents\Exception\UnsupportedResourceException;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperRegistry;

/**
 * MediaResourceConverter
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class MediaResourceConverter implements ResourceConverterInterface
{
    /**
     * @var OnlineMediaHelperRegistry
     */
    protected $onlineMediaHelperRegistry;

    public function __construct(OnlineMediaHelperRegistry $onlineMediaHelperRegistry)
    {
        $this->onlineMediaHelperRegistry = $onlineMediaHelperRegistry;
    }

    public function convert(FileInterface $file): MediaInterface
    {
        if ($this->isOnlineMedia($file)) {
            return $this->buildOnlineMedia($file);
        }

        return $this->buildMedia($file);
    }

    protected function buildMedia(FileInterface $file): Media
    {
        return new Media($file);
    }

    protected function buildOnlineMedia(FileInterface $file): OnlineMedia
    {
        $originalFile = $file;

        if (method_exists($file, 'getOriginalFile')) {
            $file = $file->getOriginalFile();
        }
        if (!($file instanceof File)) {
            throw UnsupportedResourceException::forFile($file);
        }

        // Initialize online media
        $onlineMediaHelper = $this->onlineMediaHelperRegistry->getOnlineMediaHelper($file);
        $onlineMedia = new OnlineMedia(
            $originalFile,
            $onlineMediaHelper->getOnlineMediaId($file),
            $onlineMediaHelper->getPublicUrl($file)
        );

        // Set preview image
        $previewImage = $onlineMediaHelper->getPreviewImage($file);
        $onlineMedia->setPreviewImage($previewImage);

        return $onlineMedia;
    }

    protected function isOnlineMedia(FileInterface $file): bool
    {
        return $this->onlineMediaHelperRegistry->hasOnlineMediaHelper($file->getExtension());
    }
}
