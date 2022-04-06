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

namespace Fr\Typo3HandlebarsComponents;

use Fr\Typo3HandlebarsComponents\Exception\UnsupportedMethodException;
use Fr\Typo3HandlebarsComponents\Exception\UnsupportedTypeException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * DynamicVariableInvocationTrait
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
trait DynamicVariableInvocationTrait
{
    /**
     * @param string $name
     * @param mixed[] $arguments
     * @return bool
     */
    public function __call(string $name, array $arguments): bool
    {
        $className = \get_class($this);

        // Only "is[...]" methods are supported by this class (see method annotations above)
        if ('is' !== substr($name, 0, 2)) {
            throw UnsupportedMethodException::create($name, $className);
        }

        // Convert requested page type to class constant
        $pageTypeCamelCase = substr($name, 2);
        $pageTypeUnderscored = strtoupper(GeneralUtility::camelCaseToLowerCaseUnderscored($pageTypeCamelCase));
        $constant = sprintf('%s::%s', $className, $pageTypeUnderscored);

        // Throw exception if resolved constant is not available within this class
        if (!\defined($constant)) {
            throw UnsupportedTypeException::create($constant);
        }

        return $this->is(\constant($constant));
    }

    /**
     * @param mixed $type
     * @return bool
     */
    abstract public function is($type): bool;
}
