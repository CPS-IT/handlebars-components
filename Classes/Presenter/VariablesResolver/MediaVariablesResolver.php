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

namespace Cpsit\Typo3HandlebarsComponents\Presenter\VariablesResolver;

use Cpsit\Typo3HandlebarsComponents\Data\Response\MediaProviderResponse;
use Cpsit\Typo3HandlebarsComponents\Enums\CropVariant;
use Cpsit\Typo3HandlebarsComponents\Domain\Model\Media\MediaInterface;
use Cpsit\Typo3HandlebarsComponents\Resource\ImageDimensions;
use Cpsit\Typo3HandlebarsComponents\Resource\Processing\ImageProcessingInstruction;
use Cpsit\Typo3HandlebarsComponents\Resource\Processing\ImageProcessor;

/**
 * MediaVariablesResolver
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class MediaVariablesResolver implements VariablesResolverInterface
{
    /**
     * @var array<string, ImageDimensions>
     */
    private array $dimensions = [];
    private ?MediaInterface $media = null;
    private CropVariant $cropVariant = CropVariant::Default;

    public function __construct(
        private readonly ImageProcessor $imageProcessor,
    )
    {
    }

    /**
     * @phpstan-impure
     */
    public function withMediaResponse(MediaProviderResponse $data): self
    {
        $clone = clone $this;
        $clone->media = $data->getFirstMedia();
        $clone->dimensions = $data->getImageDimensions();
        $clone->cropVariant = $data->getCropVariant();

        return $clone;
    }

    /**
     * @phpstan-impure
     */
    public function withMedia(MediaInterface $media): self
    {
        $clone = clone $this;
        $clone->media = $media;

        return $clone;
    }

    /**
     * @param array<string, ImageDimensions> $dimensions
     *
     * @phpstan-impure
     */
    public function withDimensions(array $dimensions): self
    {
        $clone = clone $this;
        $clone->dimensions = $dimensions;

        return $clone;
    }

    /**
     * @phpstan-impure
     */
    public function withCropVariant(CropVariant $cropVariant): self
    {
        $clone = clone $this;
        $clone->cropVariant = $cropVariant;

        return $clone;
    }

    /**
     * @return array{
     *     '@figure'?: array{caption: string|null, copyrightData?: array{copyright: string|null}},
     *     '@picture'?: array<string, mixed>,
     *     mediaCaption?: string|null,
     * }
     */
    public function resolve(): array
    {
        if ($this->media === null) {
            return [];
        }

        return [
            '@figure' => $this->resolveFigure($this->media),
            '@picture' => $this->resolvePicture($this->media),
            'mediaCaption' => $this->getCaption($this->media),
        ];
    }

    public function getTemplateName(): string
    {
        return '@media';
    }

    /**
     * @return array{caption: string|null, copyrightData?: array{copyright: string|null}}
     */
    private function resolveFigure(MediaInterface $media): array
    {
        $figureData = [
            'caption' => $this->getCaption($media),
        ];

        $copyright = trim((string)$media->getProperty('copyright'));

        if ($copyright !== '') {
            $figureData['copyrightData'] = [
                'copyright' => $copyright,
                'copyrightLabel' => '@todo: translatable copyright label',
                'buttonIconOnlyData' => [
                    'ariaLabel' => '@todo: translatable aria label',
                    'xtraClass' => 'js-copyright--toggle-off',
                    'type' => 'button',
                    'icon' => 'icon_close',
                ],
            ];
        }

        return $figureData;
    }

    /**
     * @return array<string, mixed>
     */
    private function resolvePicture(MediaInterface $media): array
    {
        $pictureData = [];
        foreach ($this->dimensions as $type => $dimensions) {
            $processingInstruction = new ImageProcessingInstruction($media, $dimensions, $type);
            $processingInstruction->setCropVariant($this->cropVariant->value);
            $processedFile = $this->imageProcessor->process($media, $processingInstruction);

            if ($processingInstruction->isSource()) {
                $pictureData[$type] = $processedFile->getPublicUrl();
            } else {
                $pictureData['imgData'] = [
                    'alt' => $media->getAlternative(),
                    'loading' => 'lazy',
                    'src' => $processedFile->getPublicUrl(),
                    'width' => $processedFile->getProperty('width'),
                    'height' => $processedFile->getProperty('height'),
                ];
            }
        }

        return $pictureData;
    }

    private function getCaption(MediaInterface $media): ?string
    {
        $caption = $media->getProperty('description');

        if ($caption === null) {
            $caption = $media->getProperty('caption');
        }

        return $caption;
    }
}
