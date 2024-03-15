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

namespace Cpsit\Typo3HandlebarsComponents\Exception;

use Cpsit\Typo3HandlebarsComponents\Enums\CropVariant;

/**
 * UnsupportedAspectRatioException
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class UnsupportedAspectRatioException extends Exception
{
    public static function create(CropVariant $cropVariant, string $aspectRatio): self
    {
        return new self(
            sprintf('The crop variant "%s" does not provide an aspect ratio "%s".', $cropVariant->value, $aspectRatio),
            1678806871,
        );
    }
}
