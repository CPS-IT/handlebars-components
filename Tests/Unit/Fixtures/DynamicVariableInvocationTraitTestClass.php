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

namespace Cpsit\Typo3HandlebarsComponents\Tests\Unit\Fixtures;

use Cpsit\Typo3HandlebarsComponents\DynamicVariableInvocationTrait;

/**
 * DynamicVariableInvocationTraitTestClass
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 * @internal
 *
 * @method bool isFoo()
 * @method bool isBaz()
 */
final class DynamicVariableInvocationTraitTestClass
{
    use DynamicVariableInvocationTrait;

    public const FOO = 'foo';
    public const BAZ = 'baz';

    private string $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * @param string $type
     */
    public function is($type): bool
    {
        return (string)$type === $this->type;
    }
}
