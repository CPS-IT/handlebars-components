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

namespace Fr\Typo3HandlebarsComponents\Resource\Processing;

use Fr\Typo3HandlebarsComponents\Domain\Model\Media\MediaInterface;
use Fr\Typo3HandlebarsComponents\Exception\InvalidImageDimensionException;
use Fr\Typo3HandlebarsComponents\Resource\ImageDimensions;
use TYPO3\CMS\Core\Imaging\ImageManipulation\Area;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;

/**
 * ImageProcessingInstructions
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class ImageProcessingInstruction
{
    public const DEFAULT = 'default';
    public const SOURCE = 'source';

    /**
     * @var MediaInterface
     */
    protected $media;

    /**
     * @var ImageDimensions
     */
    protected $dimensions;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string|null
     */
    protected $mediaQuery;

    /**
     * @var string
     */
    protected $cropVariant = 'default';

    /**
     * @param MediaInterface $media
     * @param ImageDimensions $dimensions
     * @param string $type
     * @throws InvalidImageDimensionException
     */
    public function __construct(MediaInterface $media, ImageDimensions $dimensions, string $type = self::DEFAULT)
    {
        $this->media = $media;
        $this->dimensions = $dimensions;
        $this->type = $type;

        $this->validate();
    }

    public function getMedia(): MediaInterface
    {
        return $this->media;
    }

    /**
     * @return int|string|null
     */
    public function getWidth()
    {
        return $this->dimensions->getWidth();
    }

    public function getNormalizedWidth(): ?int
    {
        if (!\is_string($width = $this->dimensions->getWidth())) {
            return $width;
        }

        return $this->normalizeSize($width);
    }

    /**
     * @return int|string|null
     */
    public function getHeight()
    {
        return $this->dimensions->getHeight();
    }

    public function getNormalizedHeight(): ?int
    {
        if (!\is_string($height = $this->dimensions->getHeight())) {
            return $height;
        }

        return $this->normalizeSize($height);
    }

    public function getMaxWidth(): ?int
    {
        return $this->dimensions->getMaxWidth();
    }

    public function getMaxHeight(): ?int
    {
        return $this->dimensions->getMaxHeight();
    }

    public function getDimensions(): ImageDimensions
    {
        return $this->dimensions;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isDefault(): bool
    {
        return self::DEFAULT === $this->type;
    }

    public function isSource(): bool
    {
        return 0 === strpos($this->type, self::SOURCE);
    }

    public function getMediaQuery(): ?string
    {
        return $this->mediaQuery;
    }

    public function setMediaQuery(string $mediaQuery): self
    {
        $this->mediaQuery = $mediaQuery;
        return $this;
    }

    public function getCropVariant(): string
    {
        return $this->cropVariant;
    }

    public function setCropVariant(string $cropVariant): self
    {
        $this->cropVariant = $cropVariant;
        return $this;
    }

    /**
     * @return array{width: int|string|null, height: int|string|null, maxWidth: int|null, maxHeight: int|null, crop: Area|null}
     */
    public function parse(): array
    {
        return [
            'width' => $this->getWidth(),
            'height' => $this->getHeight(),
            'maxWidth' => $this->getMaxWidth(),
            'maxHeight' => $this->getMaxHeight(),
            'crop' => $this->getCropArea(),
        ];
    }

    protected function getCropArea(): ?Area
    {
        $file = $this->media->getOriginalFile();

        if (!$file->hasProperty('crop')) {
            return null;
        }

        $cropString = $file->getProperty('crop');
        $cropVariantCollection = CropVariantCollection::create((string)$cropString);
        $cropArea = $cropVariantCollection->getCropArea($this->cropVariant);

        if ($cropArea->isEmpty()) {
            return null;
        }

        return $cropArea->makeAbsoluteBasedOnFile($file);
    }

    protected function normalizeSize(string $size): int
    {
        return (int)rtrim($size, 'cm');
    }

    /**
     * @throws InvalidImageDimensionException
     */
    protected function validate(): void
    {
        if (
            null === $this->getWidth()
            && null === $this->getHeight()
            && null === $this->getMaxWidth()
            && null === $this->getMaxHeight()
        ) {
            throw InvalidImageDimensionException::forMissingDimensions();
        }
    }
}
