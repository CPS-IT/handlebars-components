<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "handlebars_components".
 *
 * Copyright (C) 2023 Elias Häußler <e.haeussler@familie-redlich.de>
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

namespace Fr\Typo3HandlebarsComponents\Tests\Functional\Fixtures;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Exception;

/**
 * DummyExtensionConfiguration
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 * @internal
 */
final class DummyExtensionConfiguration extends ExtensionConfiguration
{
    /**
     * @var array<string, bool>
     */
    private array $activatedFeatures;

    /**
     * @param array<string, bool> $activatedFeatures
     */
    public function __construct(array $activatedFeatures)
    {
        $this->activatedFeatures = $activatedFeatures;
    }

    public function get(string $extension, string $path = ''): bool
    {
        [, $featureName] = explode('/', $path);

        if (!isset($this->activatedFeatures[$featureName])) {
            throw new Exception('dummy exception');
        }

        return $this->activatedFeatures[$featureName];
    }
}
