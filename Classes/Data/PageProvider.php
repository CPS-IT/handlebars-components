<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "wj22_sitepackage".
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

namespace Fr\Typo3HandlebarsComponents\Data;

use Fr\Typo3Handlebars\ContentObjectRendererAwareInterface;
use Fr\Typo3Handlebars\Data\DataProviderInterface;
use Fr\Typo3Handlebars\Data\Response\ProviderResponseInterface;
use Fr\Typo3Handlebars\Traits\ContentObjectRendererAwareTrait;
use Fr\Typo3HandlebarsComponents\Data\Response\PageProviderResponse;
use Fr\Typo3HandlebarsComponents\Domain\Factory\Page\PageFactory;
use Fr\Typo3HandlebarsComponents\Domain\Factory\Page\PageFooterFactoryInterface;
use Fr\Typo3HandlebarsComponents\Domain\Factory\Page\PageHeaderFactoryInterface;
use Fr\Typo3HandlebarsComponents\Renderer\Component\Page\PageContentRendererInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * PageProvider
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class PageProvider implements DataProviderInterface, ContentObjectRendererAwareInterface
{
    use ContentObjectRendererAwareTrait;

    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @var PageContentRendererInterface
     */
    protected $pageContentRenderer;

    /**
     * @var PageHeaderFactoryInterface|null
     */
    protected $pageHeaderFactory;

    /**
     * @var PageFooterFactoryInterface|null
     */
    protected $pageFooterFactory;

    public function __construct(
        PageFactory $pageFactory,
        PageContentRendererInterface $pageContentRenderer,
        PageHeaderFactoryInterface $pageHeaderFactory = null,
        PageFooterFactoryInterface $pageFooterFactory = null
    ) {
        $this->pageFactory = $pageFactory;
        $this->pageContentRenderer = $pageContentRenderer;
        $this->pageHeaderFactory = $pageHeaderFactory;
        $this->pageFooterFactory = $pageFooterFactory;
    }

    /**
     * @inheritDoc
     * @param array<string|int, mixed> $configuration
     * @return PageProviderResponse
     */
    public function get(array $data, array $configuration = []): ProviderResponseInterface
    {
        $this->assertContentObjectRendererIsAvailable();
        \assert($this->contentObjectRenderer instanceof ContentObjectRenderer);

        // Build page
        $page = $this->pageFactory->get($data);
        $response = new PageProviderResponse($page);

        // Render page content
        if ($this->pageContentRenderer instanceof ContentObjectRendererAwareInterface) {
            $this->pageContentRenderer->setContentObjectRenderer($this->contentObjectRenderer);
        }
        $response->setContent($this->pageContentRenderer->render($page, $configuration));

        // Build page header and page footer
        if ($this->pageHeaderFactory instanceof PageHeaderFactoryInterface) {
            $response->setPageHeader($this->pageHeaderFactory->get($page));
        }
        if ($this->pageFooterFactory instanceof PageFooterFactoryInterface) {
            $response->setPageFooter($this->pageFooterFactory->get($page));
        }

        return $response;
    }
}
