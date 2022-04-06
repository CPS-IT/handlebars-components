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

namespace Fr\Typo3HandlebarsComponents\Renderer\Component\Layout;

use Fr\Typo3HandlebarsComponents\Exception\UnsupportedTypeException;

/**
 * HandlebarsLayoutAction
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class HandlebarsLayoutAction
{
    public const REPLACE = 'replace';
    public const APPEND = 'append';
    public const PREPEND = 'prepend';

    /**
     * @var array<string, mixed>
     */
    protected $data;

    /**
     * @var callable
     */
    protected $renderFunction;

    /**
     * @var string
     */
    protected $mode;

    /**
     * @param array<string, mixed> $data
     * @param callable $renderFunction
     * @param string $mode
     */
    public function __construct(array $data, callable $renderFunction, string $mode = self::REPLACE)
    {
        $this->data = $data;
        $this->renderFunction = $renderFunction;
        $this->mode = strtolower($mode);
        $this->validate();
    }

    public function render(string $value): string
    {
        $renderResult = ($this->renderFunction)($this->data);

        switch ($this->mode) {
            case self::APPEND:
                return $value . $renderResult;
            case self::PREPEND:
                return $renderResult . $value;
            case self::REPLACE:
                return $renderResult;
            default:
                throw UnsupportedTypeException::create($this->mode);
        }
    }

    /**
     * @return string[]
     */
    protected function getSupportedModes(): array
    {
        return [
            self::REPLACE,
            self::APPEND,
            self::PREPEND,
        ];
    }

    protected function validate(): void
    {
        if (!\in_array($this->mode, $this->getSupportedModes(), true)) {
            throw UnsupportedTypeException::create($this->mode);
        }
    }
}
