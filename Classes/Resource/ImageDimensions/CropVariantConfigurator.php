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
use Cpsit\Typo3HandlebarsComponents\Extension;
use Fr\Typo3ConfigProxy\Config\Localization;

/**
 * CropVariantConfigurator
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class CropVariantConfigurator
{
    private function __construct(
        private readonly Localization $localization,
    ) {}

    public static function create(): self
    {
        return new self(
            Localization::forExtension(Extension::KEY),
        );
    }

    /**
     * @param array<string, float> $aspectRatios
     * @return array{config: array{cropVariants: array<value-of<CropVariant>, array<string, mixed>>}}
     */
    public function get(array $aspectRatios, CropVariant $cropVariant): array
    {
        array_walk(
            $aspectRatios,
            fn(float &$value, string $title) => $this->mapAspectRatio($cropVariant, $value, $title),
        );

        return [
            'config' => [
                'cropVariants' => [
                    CropVariant::Default->value => [
                        'disabled' => true,
                    ],
                    $cropVariant->value => [
                        'title' => $this->translateCropVariant($cropVariant, 'title'),
                        'allowedAspectRatios' => $aspectRatios,
                    ],
                ],
            ],
        ];
    }

    private function mapAspectRatio(CropVariant $cropVariant, float &$value, string $title): void
    {
        $value = [
            'title' => $this->translateCropVariant($cropVariant, str_replace(':', '_', $title)),
            'value' => $value,
        ];
    }

    private function translateCropVariant(CropVariant $cropVariant, string $property): string
    {
        return $this->localization->forField(
            'crop',
            'sys_file_reference',
            'cropVariants.' . $cropVariant->value . '.' . $property,
        );
    }
}
