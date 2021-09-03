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

namespace Fr\Typo3HandlebarsComponents\Domain\Factory\Page;

use Fr\Typo3HandlebarsComponents\Domain\Model\Page;
use Fr\Typo3HandlebarsComponents\Domain\Model\Page\PageType;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Frontend\Page\PageLayoutResolver;

/**
 * PageFactory
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class PageFactory
{
    /**
     * @var PageLayoutResolver
     */
    protected $pageLayoutResolver;

    public function __construct(PageLayoutResolver $pageLayoutResolver)
    {
        $this->pageLayoutResolver = $pageLayoutResolver;
    }

    /**
     * @param array<string, mixed> $data
     * @return Page
     */
    public function get(array $data): Page
    {
        $page = new Page($data['uid'], $this->determinePageType($data));

        return $page
            ->setTitle($data['title'])
            ->setSubtitle($data['subtitle'])
            ->setLayout($this->determineLayout($data));

        // @todo add more properties
    }

    /**
     * @param array<string, mixed> $data
     * @return PageType
     */
    protected function determinePageType(array $data): PageType
    {
        return new PageType((int)$data['doktype']);
    }

    /**
     * @param array<string, mixed> $data
     * @return string
     */
    protected function determineLayout(array $data): string
    {
        $rootline = GeneralUtility::makeInstance(RootlineUtility::class, $data['uid'])->get();

        return $this->pageLayoutResolver->getLayoutForPage($data, $rootline);
    }
}
