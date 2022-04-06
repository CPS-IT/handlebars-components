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

namespace Fr\Typo3HandlebarsComponents\Presenter;

use Fr\Typo3Handlebars\Data\Response\ProviderResponseInterface;
use Fr\Typo3Handlebars\Exception\UnableToPresentException;
use Fr\Typo3Handlebars\Presenter\AbstractPresenter;
use Fr\Typo3Handlebars\Renderer\RendererInterface;
use Fr\Typo3HandlebarsComponents\Data\Response\PageProviderResponse;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\ArrayUtility;

/**
 * AbstractPagePresenter
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
abstract class AbstractPagePresenter extends AbstractPresenter
{
    /**
     * @var string
     */
    protected $templateName = '@cms';

    /**
     * @var PageRenderer
     */
    protected $pageRenderer;

    public function __construct(RendererInterface $renderer, PageRenderer $pageRenderer)
    {
        parent::__construct($renderer);
        $this->pageRenderer = $pageRenderer;
    }

    public function present(ProviderResponseInterface $data): string
    {
        if (!($data instanceof PageProviderResponse)) {
            throw new UnableToPresentException('Received unexpected response from provider.', 1616155696);
        }

        $headerAssets = $this->renderHeaderAssets($data);
        $this->pageRenderer->addHeaderData($headerAssets);
        $footerAssets = $this->renderFooterAssets($data);
        $this->pageRenderer->addFooterData($footerAssets);

        $renderData = [
            'templateName' => $this->determineTemplateName($data),
            'contentName' => $this->getContentBlockName(),
            'renderedContent' => $data->getContent(),
        ];
        ArrayUtility::mergeRecursiveWithOverrule($renderData, $this->getAdditionalRenderData($data));

        return $this->renderer->render($this->templateName, $renderData);
    }

    abstract protected function determineTemplateName(PageProviderResponse $data): string;

    protected function getContentBlockName(): string
    {
        return 'mainContent';
    }

    /**
     * @return array<string, mixed>
     * @noinspection PhpUnusedParameterInspection
     */
    protected function getAdditionalRenderData(PageProviderResponse $data): array
    {
        return [];
    }

    protected function renderHeaderAssets(PageProviderResponse $data): string
    {
        return '';
    }

    protected function renderFooterAssets(PageProviderResponse $data): string
    {
        return '';
    }
}
