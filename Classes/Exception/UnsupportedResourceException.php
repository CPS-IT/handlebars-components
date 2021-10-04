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

namespace Fr\Typo3HandlebarsComponents\Exception;

use Fr\Typo3HandlebarsComponents\Domain\Model\Media\MediaInterface;
use TYPO3\CMS\Core\Resource\FileInterface;

/**
 * UnsupportedResourceException
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class UnsupportedResourceException extends \Exception
{
    public static function forFile(FileInterface $file): self
    {
        return new self(
            sprintf('The file "%s" is not supported.', $file->getIdentifier()),
            1633012917
        );
    }

    public static function forMedia(MediaInterface $media): self
    {
        return new self(
            sprintf('The media "%s" is not supported.', $media->getName()),
            1633015669
        );
    }
}
