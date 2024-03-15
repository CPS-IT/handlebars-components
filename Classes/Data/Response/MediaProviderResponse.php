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

namespace Cpsit\Typo3HandlebarsComponents\Data\Response;

use Cpsit\Typo3HandlebarsComponents\Enums\CropVariant;
use Cpsit\Typo3HandlebarsComponents\Enums\MediaOrientation;
use Fr\Typo3Handlebars\Data\Response\ProviderResponseInterface;
use Cpsit\Typo3HandlebarsComponents\Domain\Model\Media\MediaInterface;
use Cpsit\Typo3HandlebarsComponents\Resource\ImageDimensions;

/**
 * MediaProviderResponse
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class MediaProviderResponse implements ProviderResponseInterface
{
    /**
     * @param MediaInterface[] $media
     * @param array<string, ImageDimensions> $imageDimensions
     */
    public function __construct(
        private readonly array $media,
        private ?MediaOrientation $orientation = null,
        private array $imageDimensions = [],
        private CropVariant $cropVariant = CropVariant::Default,
    ) {}

    /**
     * @return MediaInterface[]
     */
    public function getMedia(): array
    {
        return $this->media;
    }

    public function getFirstMedia(): ?MediaInterface
    {
        foreach ($this->media as $media) {
            return $media;
        }

        return null;
    }

    /**
     * @phpstan-assert-if-true !array{} $this->getMedia()
     * @phpstan-assert-if-true !null $this->getFirstMedia()
     */
    public function hasMedia(): bool
    {
        return $this->media !== [];
    }

    public function getOrientation(): ?MediaOrientation
    {
        return $this->orientation;
    }

    public function setOrientation(MediaOrientation $orientation): self
    {
        $this->orientation = $orientation;

        return $this;
    }

    /**
     * @return array<string, ImageDimensions>
     */
    public function getImageDimensions(): array
    {
        return $this->imageDimensions;
    }

    /**
     * @param array<string, ImageDimensions> $imageDimensions
     */
    public function setImageDimensions(array $imageDimensions): self
    {
        $this->imageDimensions = $imageDimensions;

        return $this;
    }

    public function getCropVariant(): CropVariant
    {
        return $this->cropVariant;
    }

    public function setCropVariant(CropVariant $cropVariant): self
    {
        $this->cropVariant = $cropVariant;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'media' => $this->media,
            'orientation' => $this->orientation,
            'imageDimensions' => $this->imageDimensions,
            'cropVariant' => $this->cropVariant,
        ];
    }
}
