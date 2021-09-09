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

namespace Fr\Typo3HandlebarsComponents\Utility;

use Fr\Typo3HandlebarsComponents\Exception\InvalidConfigurationException;
use TYPO3\CMS\Core\Utility\StringUtility;

/**
 * TypoScriptUtility
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class TypoScriptUtility
{
    /**
     * @param string $path
     * @return string[]
     */
    public static function transformArrayPathToTypoScriptArrayPath(string $path): array
    {
        return preg_split('/(?<=[.!?])/', $path, 0, PREG_SPLIT_NO_EMPTY) ?: [];
    }

    /**
     * @param array<string, mixed> $typoScriptArray
     * @param string $path
     * @throws InvalidConfigurationException
     */
    public static function validateTypoScriptArray(array $typoScriptArray, string $path = ''): void
    {
        foreach ($typoScriptArray as $key => $value) {
            $keyHasTrailingDot = StringUtility::endsWith($key, '.');
            if (!is_array($value)) {
                $path .= $key;
                if ($keyHasTrailingDot) {
                    throw InvalidConfigurationException::create($path);
                }
            } elseif (!$keyHasTrailingDot) {
                throw InvalidConfigurationException::create($path . $key);
            } else {
                self::validateTypoScriptArray($value, $path . $key);
            }
        }
    }
}
