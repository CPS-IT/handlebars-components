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

namespace Cpsit\Typo3HandlebarsComponents\Exception;

/**
 * InvalidImageDimensionException
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class InvalidImageDimensionException extends \Exception
{
    /**
     * @param mixed $dimension
     */
    public static function create($dimension): self
    {
        return new self(
            sprintf('Image dimensions must be of type integer or string, %s given.', get_debug_type($dimension)),
            1631807380
        );
    }

    public static function forMissingDimensions(): self
    {
        return new self(
            'No image dimensions defined. You must define at least one image dimension, e.g. width or height.',
            1649237990
        );
    }

    public static function forUnresolvableDimension(string $dimension): self
    {
        return new self(
            sprintf('Image sizes must be integers, optionally followed by "c" or "m", "%s" given.', $dimension),
            1631807435
        );
    }
}
