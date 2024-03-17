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

namespace Cpsit\Typo3HandlebarsComponents\Data;

use Cpsit\Typo3HandlebarsComponents\Data\Response\MediaProviderResponse;
use Cpsit\Typo3HandlebarsComponents\Enums\MediaOrientation;
use Cpsit\Typo3HandlebarsComponents\Exception\UnsupportedMediaOrientationException;
use Cpsit\Typo3HandlebarsComponents\Resource\ImageDimensions\MediaImageDimensions;
use Fr\Typo3Handlebars\Data\DataProviderInterface;
use Fr\Typo3Handlebars\Data\Response\ProviderResponseInterface;
use Cpsit\Typo3HandlebarsComponents\Service\MediaService;

/**
 * MediaProvider
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class MediaProvider implements DataProviderInterface
{
    protected string $mediaFieldName = 'image';
    protected string $tableName = 'tt_content';

    public function __construct(
        private readonly MediaService $mediaService,
    ) {}

    /**
     * @return MediaProviderResponse
     */
    public function get(array $data): ProviderResponseInterface
    {
        $media = $this->mediaService->getFromRelation(
            $this->mediaFieldName,
            $this->tableName,
            $data
        );

        $orientation = $this->createMediaOrientation($data['imageorient'] ?? null);
        $imageDimensions = MediaImageDimensions::getForAspectRatio(MediaImageDimensions::ASPECT_RATIO_NONE);

        return new MediaProviderResponse($media, $orientation, $imageDimensions);
    }

    /**
     * @phpstan-impure
     */
    public function withMediaFieldName(string $mediaFieldName): self
    {
        $clone = clone $this;
        $clone->mediaFieldName = $mediaFieldName;

        return $clone;
    }

    public function forTable(string $tableName): self
    {
        $clone = clone $this;
        $clone->tableName = $tableName;
        return $clone;
    }

    private function createMediaOrientation(string|int|null $imageOrientation): ?MediaOrientation
    {
        if (!is_numeric($imageOrientation)) {
            return null;
        }

        try {
            return MediaOrientation::fromIdentifier((int)$imageOrientation);
        } catch (UnsupportedMediaOrientationException) {
            return null;
        }
    }
}
