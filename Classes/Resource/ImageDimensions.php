<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "handlebars_components".
 *
 * Copyright (C) 2022 Elias Häußler <e.haeussler@familie-redlich.de>
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

namespace Fr\Typo3HandlebarsComponents\Resource;

use Fr\Typo3HandlebarsComponents\Exception\InvalidImageDimensionException;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * ImageDimensions
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class ImageDimensions
{
    /**
     * @var int|string|null
     */
    protected $width;

    /**
     * @var int|string|null
     */
    protected $height;

    /**
     * @var int|null
     */
    protected $maxWidth;

    /**
     * @var int|null
     */
    protected $maxHeight;

    /**
     * @param int|string|null $width
     * @param int|string|null $height
     * @throws InvalidImageDimensionException
     */
    public function __construct($width = null, $height = null)
    {
        $this->setWidth($width);
        $this->setHeight($height);
    }

    public static function create(): self
    {
        return new self();
    }

    /**
     * @return int|string|null
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int|string|null $width
     * @throws InvalidImageDimensionException
     */
    public function setWidth($width): self
    {
        $this->width = $this->parseSize($width);
        return $this;
    }

    /**
     * @return int|string|null
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int|string|null $height
     * @throws InvalidImageDimensionException
     */
    public function setHeight($height): self
    {
        $this->height = $this->parseSize($height);
        return $this;
    }

    public function getMaxWidth(): ?int
    {
        return $this->maxWidth;
    }

    public function setMaxWidth(int $maxWidth): self
    {
        $this->maxWidth = $maxWidth;
        return $this;
    }

    public function getMaxHeight(): ?int
    {
        return $this->maxHeight;
    }

    public function setMaxHeight(int $maxHeight): self
    {
        $this->maxHeight = $maxHeight;
        return $this;
    }

    /**
     * @param string|int|null $size
     * @return string|int|null
     * @throws InvalidImageDimensionException
     */
    protected function parseSize($size)
    {
        if ($size === null) {
            return null;
        }

        if (\is_int($size)) {
            return $size;
        }

        // Validate given size
        if (!\is_string($size)) {
            throw InvalidImageDimensionException::create($size);
        }

        // Validate normalized size
        $normalizedSize = rtrim($size, 'cm');
        if (!MathUtility::canBeInterpretedAsInteger($normalizedSize)) {
            throw InvalidImageDimensionException::forUnresolvableDimension($size);
        }

        return $size;
    }
}
