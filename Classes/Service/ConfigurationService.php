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

namespace Cpsit\Typo3HandlebarsComponents\Service;

use Cpsit\Typo3HandlebarsComponents\Utility\TypoScriptUtility;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * ConfigurationService
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class ConfigurationService implements SingletonInterface
{
    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @var array<string, mixed|null>
     */
    protected $configurationCache = [];

    public function __construct(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * Get TypoScript configuration at given path.
     *
     * Returns the cached TypoScript configuration at the given path. Note that the raw
     * TypoScript configuration is taken into account. That means, you need to access
     * the configuration using dot-notation as configuration path, e.g. "page.10.userFunc".
     * The resolved configuration value is returned in TypoScript dot-notation as well.
     *
     * Example
     * =======
     *
     * TypoScript:
     *
     * page {
     *   10 = USER
     *   10 {
     *     userFunc = Vendor\Extension\UserFunc\MyUserFunc->method
     *   }
     * }
     *
     * PHP:
     *
     * $configurationService->get('page'); // returns NULL
     * $configurationService->get('page.'); // returns ['10' => 'USER', '10.' => ['userFunc' => 'Vendor\Extension\UserFunc\MyUserFunc->method']]
     * $configurationService->get('page.10.userFunc'); // returns 'Vendor\Extension\UserFunc\MyUserFunc->method'
     *
     * @param string $path TypoScript configuration path to be returned
     * @return mixed|null Resolved TypoScript configuration at given path
     */
    public function get(string $path)
    {
        if (!\array_key_exists($path, $this->configurationCache)) {
            try {
                $configuration = $this->configurationManager->getConfiguration(
                    ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
                );
                $pathSegments = TypoScriptUtility::transformArrayPathToTypoScriptArrayPath($path);
                $this->configurationCache[$path] = ArrayUtility::getValueByPath($configuration, $pathSegments);
            } catch (\Exception $e) {
                $this->configurationCache[$path] = null;
            }
        }

        return $this->configurationCache[$path];
    }
}
