<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "handlebars_components".
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

namespace Fr\Typo3HandlebarsComponents\Data\Response;

use Fr\Typo3Handlebars\Data\Response\ProviderResponseInterface;
use Fr\Typo3HandlebarsComponents\Domain\Model\Page;
use Fr\Typo3HandlebarsComponents\Domain\Model\Page\PageFooterInterface;
use Fr\Typo3HandlebarsComponents\Domain\Model\Page\PageHeaderInterface;

/**
* PageProviderResponse
*
 * @author Martin Adler <m.adler@familie-redlich.de>
 * @license GPL-2.0-or-later
*/
class PageProviderResponse implements ProviderResponseInterface
{
    /**
     * @var Page
     */
    protected $page;

    /**
     * @var string
     */
    protected $content = '';

    /**
     * @var PageHeaderInterface|null
     */
    protected $pageHeader;

    /**
     * @var PageFooterInterface|null
     */
    protected $pageFooter;

    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    public function getPage(): Page
    {
        return $this->page;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): PageProviderResponse
    {
        $this->content = $content;
        return $this;
    }

    public function getPageHeader(): ?PageHeaderInterface
    {
        return $this->pageHeader;
    }

    public function setPageHeader(?PageHeaderInterface $pageHeader): PageProviderResponse
    {
        $this->pageHeader = $pageHeader;
        return $this;
    }

    public function getPageFooter(): ?PageFooterInterface
    {
        return $this->pageFooter;
    }

    public function setPageFooter(?PageFooterInterface $pageFooter): PageProviderResponse
    {
        $this->pageFooter = $pageFooter;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'page' => $this->page,
            'content' => $this->content,
            'pageHeader' => $this->pageHeader,
            'pageFooter' => $this->pageFooter,
        ];
    }
}
