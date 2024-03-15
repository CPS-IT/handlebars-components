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
use Cpsit\Typo3HandlebarsComponents\Resource\Processing\ImageProcessingInstruction;

/**
 * MediaImageDimensions
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-3.0-or-later
 */
final class MediaImageDimensions extends BaseImageDimensions
{
    public const ASPECT_RATIO_NONE = 'none';

    protected const CROP_VARIANT = CropVariant::Default;
    protected const IMAGE_DIMENSIONS = [
        self::ASPECT_RATIO_NONE => [
            'sourceL' => [
                'width' => '1280c',
            ],
            'sourceM' => [
                'width' => '640c',
            ],
            'sourceS' => [
                'width' => '320c',
            ],
            ImageProcessingInstruction::DEFAULT => [
                'width' => '320c',
            ],
        ],
    ];
}
