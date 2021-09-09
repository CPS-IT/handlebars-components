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

namespace Fr\Typo3HandlebarsComponents\Domain\Model\Dto;

use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

/**
 * LanguageMenuItem
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class LanguageMenuItem extends MenuItem
{
    /**
     * @var SiteLanguage
     */
    protected $siteLanguage;

    /**
     * @var bool
     */
    protected $available = false;

    public function __construct(Link $link, SiteLanguage $siteLanguage)
    {
        parent::__construct($link);
        $this->siteLanguage = $siteLanguage;
    }

    public function getSiteLanguage(): SiteLanguage
    {
        return $this->siteLanguage;
    }

    public function setSiteLanguage(SiteLanguage $siteLanguage): self
    {
        $this->siteLanguage = $siteLanguage;
        return $this;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): self
    {
        $this->available = $available;
        return $this;
    }
}
