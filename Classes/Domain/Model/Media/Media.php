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

namespace Fr\Typo3HandlebarsComponents\Domain\Model\Media;

use TYPO3\CMS\Core\Resource\FileInterface;

/**
 * Media
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class Media implements MediaInterface
{
    /**
     * @var FileInterface
     */
    protected $file;

    public function __construct(FileInterface $file)
    {
        $this->file = $file;
    }

    public function getOriginalFile(): FileInterface
    {
        return $this->file;
    }

    public function getName(): string
    {
        return $this->file->getNameWithoutExtension();
    }

    public function getExtension(): string
    {
        return $this->file->getExtension();
    }

    public function getAlternative(): string
    {
        $alternative = null;

        if ($this->file->hasProperty('alternative')) {
            $alternative = $this->file->getProperty('alternative');
        }
        if ($alternative === null && $this->file->hasProperty('title')) {
            $alternative = $this->file->getProperty('title');
        }
        if ($alternative === null) {
            $alternative = $this->file->getNameWithoutExtension();
        }

        return $alternative;
    }

    public function getProperty(string $propertyName)
    {
        return $this->file->getProperty($propertyName);
    }

    public function hasProperty(string $propertyName): bool
    {
        return $this->file->hasProperty($propertyName);
    }
}
