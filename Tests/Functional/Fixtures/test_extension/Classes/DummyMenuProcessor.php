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

namespace Cpsit\Typo3HandlebarsComponentsTestExtension;

use Cpsit\Typo3HandlebarsComponents\Domain\Model\Dto\Link;
use Cpsit\Typo3HandlebarsComponents\Domain\Model\Dto\Menu;
use Cpsit\Typo3HandlebarsComponents\Domain\Model\Dto\MenuItem;
use Cpsit\Typo3HandlebarsComponents\Service\Configuration\MenuConfiguration;
use Cpsit\Typo3HandlebarsComponents\Service\MenuService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentDataProcessor;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * DummyMenuProcessor
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 * @internal
 */
final class DummyMenuProcessor implements DataProcessorInterface
{
    public ContentObjectRenderer $cObj;

    /**
     * @param array<string, mixed> $contentObjectConfiguration
     * @param array<string, mixed> $processorConfiguration
     * @param array<string, mixed> $processedData
     * @return array<string, mixed>
     */
    public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData): array
    {
        $processedData['menu'] = new Menu('example', [
            new MenuItem(new Link('https://www.example.com', 'example EN')),
            new MenuItem(new Link('https://www.example.com/de', 'example DE')),
            new MenuItem(new Link('https://www.example.com/es', 'example ES')),
        ]);

        return $processedData;
    }

    /**
     * @param array<string, mixed> $configuration
     */
    public function preProcess(string $content, array $configuration): string
    {
        $menuService = new MenuService($this->cObj, new ContentDataProcessor(GeneralUtility::getContainer()));
        $configuration = new MenuConfiguration(
            json_decode($configuration['userFunc.']['menuConfiguration'], true) ?: [],
            $configuration['userFunc.']['menuType'] ?: MenuConfiguration::DEFAULT
        );
        $menu = $menuService->buildMenu($configuration);

        $jsonArray = $this->convertMenuItemsToJsonArray($menu->getItems());

        return json_encode($jsonArray, JSON_THROW_ON_ERROR);
    }

    /**
     * @param MenuItem[] $menuItems
     * @return array<int, array<string, mixed>>
     */
    protected function convertMenuItemsToJsonArray(array $menuItems): array
    {
        $jsonArray = [];

        foreach ($menuItems as $menuItem) {
            $jsonArray[] = [
                'link' => $menuItem->getLink()->getUrl(),
                'active' => $menuItem->isActive(),
                'current' => $menuItem->isCurrent(),
                'subItems' => $this->convertMenuItemsToJsonArray($menuItem->getSubItems()),
            ];
        }

        return $jsonArray;
    }
}
