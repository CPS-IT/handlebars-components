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

namespace Cpsit\Typo3HandlebarsComponents\Enums;

use Cpsit\Typo3HandlebarsComponents\Exception\UnsupportedMediaOrientationException;

/**
 * MediaOrientation
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
enum MediaOrientation: string
{
    case Above = 'above';
    case Below = 'below';
    // left modifier used in TextMediaElement
    case InTextLeft = 'left';
    case BesideLeft = 'beside-left';
    case BesideRight = 'beside-right';

    /**
     * @throws UnsupportedMediaOrientationException
     */
    public static function fromIdentifier(int $identifier): self
    {
        return match ($identifier) {
            0 => MediaOrientation::Above,
            8 => MediaOrientation::Below,
            18 => MediaOrientation::InTextLeft,
            25 => MediaOrientation::BesideRight,
            26 => MediaOrientation::BesideLeft,
            default => throw UnsupportedMediaOrientationException::create($identifier),
        };
    }
}
