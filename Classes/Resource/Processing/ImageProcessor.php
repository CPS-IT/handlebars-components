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

namespace Cpsit\Typo3HandlebarsComponents\Resource\Processing;

use Cpsit\Typo3HandlebarsComponents\Domain\Model\Media\MediaInterface;
use Cpsit\Typo3HandlebarsComponents\Exception\UnsupportedResourceException;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\ProcessedFile;

/**
 * ImageProcessor
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class ImageProcessor
{
    public function process(MediaInterface $media, ImageProcessingInstruction $processingInstruction): ProcessedFile
    {
        $file = $media->getOriginalFile();
        $originalFile = $this->resolveOriginalFile($file);

        return $originalFile->process(ProcessedFile::CONTEXT_IMAGECROPSCALEMASK, $processingInstruction->parse());
    }

    protected function resolveOriginalFile(FileInterface $file): File
    {
        if (method_exists($file, 'getOriginalFile')) {
            $file = $file->getOriginalFile();
        }

        if (!($file instanceof File)) {
            throw UnsupportedResourceException::forFile($file);
        }

        return $file;
    }
}
