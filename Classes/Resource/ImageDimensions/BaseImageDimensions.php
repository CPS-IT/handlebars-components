<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "handlebars_component".
 *
 * Copyright (C) 2023 Elias Häußler <e.haeussler@familie-redlich.de>
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

namespace Cpsit\Typo3HandlebarsComponents\Resource\ImageDimensions;

use Cpsit\Typo3HandlebarsComponents\Enums\CropVariant;
use Cpsit\Typo3HandlebarsComponents\Exception\UnsupportedAspectRatioException;
use Cpsit\Typo3HandlebarsComponents\Exception\InvalidImageDimensionException;
use Cpsit\Typo3HandlebarsComponents\Resource\ImageDimensions;

/**
 * BaseImageDimensions
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
abstract class BaseImageDimensions
{
    /** @var CropVariant */
    protected const CROP_VARIANT = CropVariant::Default;

    /** @var array<string, array<string, array<string, string|int>>> */
    protected const IMAGE_DIMENSIONS = [];

    /**
     * @return array<string, array<string, ImageDimensions>>
     * @throws InvalidImageDimensionException
     */
    final public static function get(): array
    {
        return static::constructDimensions();
    }

    /**
     * @return array<string, ImageDimensions>
     * @throws InvalidImageDimensionException
     * @throws UnsupportedAspectRatioException
     */
    final public static function getForAspectRatio(string $aspectRatio): array
    {
        return static::get()[$aspectRatio]
            ?? throw UnsupportedAspectRatioException::create(static::CROP_VARIANT, $aspectRatio)
        ;
    }

    /**
     * @return array<string, array<string, ImageDimensions>>
     * @throws InvalidImageDimensionException
     */
    protected static function constructDimensions(): array
    {
        $imageDimensions = [];

        foreach (static::IMAGE_DIMENSIONS as $aspectRatio => $dimensions) {
            $imageDimensions[$aspectRatio] = [];

            foreach ($dimensions as $type => $dimension) {
                $width = $dimension['width'] ?? null;
                $height = $dimension['height'] ?? null;

                $imageDimensions[$aspectRatio][$type] = ImageDimensions::create()
                    ->setWidth($width)
                    ->setHeight($height)
                ;
            }
        }

        return $imageDimensions;
    }
}
