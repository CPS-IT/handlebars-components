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

namespace Fr\Typo3HandlebarsComponents\Domain\Model;

use Fr\Typo3HandlebarsComponents\Domain\Model\Page\PageType;

/**
 * Page
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class Page
{
    public const TABLE_NAME = 'pages';

    /**
     * @var int
     */
    protected $id;

    /**
     * @var PageType
     */
    protected $pageType;

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $subtitle = '';

    /**
     * @var string
     */
    protected $layout = 'default';

    public function __construct(int $id, PageType $pageType)
    {
        $this->id = $id;
        $this->pageType = $pageType;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPageType(): PageType
    {
        return $this->pageType;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    public function setSubtitle(string $subtitle): self
    {
        $this->subtitle = $subtitle;
        return $this;
    }

    public function getLayout(): string
    {
        return $this->layout;
    }

    public function setLayout(string $layout): self
    {
        $this->layout = $layout;
        return $this;
    }
}
