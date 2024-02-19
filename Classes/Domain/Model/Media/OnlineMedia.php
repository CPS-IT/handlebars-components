<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "handlebars_components".
 *
 * Copyright (C) 2021 Elias Häußler <e.haeussler@familie-redlich.de>
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

namespace Cpsit\Typo3HandlebarsComponents\Domain\Model\Media;

use TYPO3\CMS\Core\Resource\FileInterface;

/**
 * OnlineMedia
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class OnlineMedia extends Media
{
    /**
     * @var string
     */
    protected $onlineMediaId;

    /**
     * @var string
     */
    protected $publicUrl;

    /**
     * @var string|null
     */
    protected $previewImage;

    public function __construct(FileInterface $file, string $onlineMediaId, string $publicUrl)
    {
        parent::__construct($file);
        $this->onlineMediaId = $onlineMediaId;
        $this->publicUrl = $publicUrl;
    }

    public function getOnlineMediaId(): string
    {
        return $this->onlineMediaId;
    }

    public function getPublicUrl(): string
    {
        return $this->publicUrl;
    }

    public function getPreviewImage(): ?string
    {
        return $this->previewImage;
    }

    public function setPreviewImage(string $previewImage): self
    {
        $this->previewImage = $previewImage;
        return $this;
    }
}
