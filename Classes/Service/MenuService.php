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

namespace Fr\Typo3HandlebarsComponents\Service;

use Fr\Typo3HandlebarsComponents\Domain\Model\Dto\LanguageMenuItem;
use Fr\Typo3HandlebarsComponents\Domain\Model\Dto\Link;
use Fr\Typo3HandlebarsComponents\Domain\Model\Dto\Menu;
use Fr\Typo3HandlebarsComponents\Domain\Model\Dto\MenuItem;
use Fr\Typo3HandlebarsComponents\Exception\UnresolvedSiteException;
use Fr\Typo3HandlebarsComponents\ServerRequestTrait;
use Fr\Typo3HandlebarsComponents\Service\Configuration\MenuConfiguration;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentDataProcessor;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\DataProcessing\LanguageMenuProcessor;
use TYPO3\CMS\Frontend\DataProcessing\MenuProcessor;

/**
 * MenuService
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class MenuService
{
    use ServerRequestTrait;

    /**
     * @var ContentObjectRenderer
     */
    protected $contentObjectRenderer;

    /**
     * @var ContentDataProcessor
     */
    protected $contentDataProcessor;

    public function __construct(ContentObjectRenderer $contentObjectRenderer, ContentDataProcessor $contentDataProcessor)
    {
        $this->contentObjectRenderer = $contentObjectRenderer;
        $this->contentDataProcessor = $contentDataProcessor;
    }

    public function buildMenu(MenuConfiguration $configuration): Menu
    {
        $processedMenu = $this->processMenu($configuration);

        // Early return if menu is already built which might happen if a custom data
        // processing takes place. In this case, no further transformation/processing
        // is required.
        if ($processedMenu instanceof Menu) {
            return $processedMenu;
        }

        return new Menu($configuration->getType(), $this->transformProcessedMenu($configuration, $processedMenu));
    }

    /**
     * @return array<int, array<string, mixed>>|Menu
     */
    protected function processMenu(MenuConfiguration $configuration)
    {
        $processorConfiguration = $configuration->getTypoScriptConfiguration();
        $processorConfiguration['as'] ??= 'menu';
        $targetVariableName = $processorConfiguration['as'];

        // Process menu using an appropriate DataProcessor.
        // We need to create the processors on demand since they need to be stateless
        // and therefore cannot be injected via service container.
        switch ($configuration->getType()) {
            // Language menu
            case MenuConfiguration::LANGUAGE:
                $dataProcessor = GeneralUtility::makeInstance(LanguageMenuProcessor::class);
                $processedVariables = $dataProcessor->process($this->contentObjectRenderer, [], $processorConfiguration, []);
                break;

            // Custom menu (calls custom data processors)
            case MenuConfiguration::CUSTOM:
                $processedVariables = $this->contentDataProcessor->process($this->contentObjectRenderer, $processorConfiguration, []);
                break;

            // Default menu
            default:
                $dataProcessor = GeneralUtility::makeInstance(MenuProcessor::class);
                $processedVariables = $dataProcessor->process($this->contentObjectRenderer, [], $processorConfiguration, []);
                break;
        }

        return $processedVariables[$targetVariableName] ?? [];
    }

    /**
     * @param array<int, array<string, mixed>> $processedMenu
     * @return MenuItem[]
     */
    protected function transformProcessedMenu(MenuConfiguration $configuration, array $processedMenu): array
    {
        $menuItems = [];

        foreach ($processedMenu as $processedMenuItem) {
            $menuItem = $this->initializeMenuItem($configuration->getType(), $processedMenuItem);

            // Set active/current state
            $menuItem->setActive((bool)$processedMenuItem['active']);
            $menuItem->setCurrent((bool)$processedMenuItem['current']);

            // Transform sub-items
            if (isset($processedMenuItem['children'])) {
                $menuItem->setSubItems($this->transformProcessedMenu($configuration, $processedMenuItem['children']));
            }

            $menuItems[] = $menuItem;
        }

        return $menuItems;
    }

    /**
     * @param array<string, mixed> $processedMenuItem
     */
    protected function initializeMenuItem(string $menuType, array $processedMenuItem): MenuItem
    {
        $link = new Link(
            $processedMenuItem['link'],
            $processedMenuItem['title'],
            $processedMenuItem['target'] ?: null
        );

        switch ($menuType) {
            // Language menu
            case MenuConfiguration::LANGUAGE:
                $siteLanguage = $this->resolveSiteLanguage($processedMenuItem['languageId']);
                $menuItem = new LanguageMenuItem($link, $siteLanguage);
                $menuItem->setAvailable((bool)$processedMenuItem['available']);
                break;

            // Custom/Default menu
            case MenuConfiguration::CUSTOM:
            default:
                $menuItem = new MenuItem($link);
                break;
        }

        return $menuItem;
    }

    protected function resolveSiteLanguage(int $languageId): SiteLanguage
    {
        $site = self::getServerRequest()->getAttribute('site');

        // We need to make sure the current server request contains the resolved
        // site, otherwise we're unable to retrieve a SiteLanguage object
        if (!($site instanceof Site)) {
            throw UnresolvedSiteException::create();
        }

        return $site->getLanguageById($languageId);
    }
}
