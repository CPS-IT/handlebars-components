<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "fr/handlebars_components".
 *
 * Copyright (C) 2021 Martin Adler <m.adler@familie-redlich.de>
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

namespace Fr\Typo3HandlebarsComponents\Domain\Model\Dto;

/**
 * Link
 *
 * @author Martin Adler <m.adler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class Link
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string|null
     */
    protected $target;

    private function __construct(string $url, string $label, string $target = null)
    {
        $this->url = $url;
        $this->label = $label;
        $this->target = $target;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }
}
