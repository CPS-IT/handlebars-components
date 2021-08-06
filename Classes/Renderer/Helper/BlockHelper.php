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

namespace Fr\Typo3HandlebarsComponents\Renderer\Helper;

use Fr\Typo3Handlebars\Renderer\Helper\HelperInterface;
use Fr\Typo3HandlebarsComponents\Renderer\Component\Layout\HandlebarsLayout;
use Fr\Typo3HandlebarsComponents\Renderer\Component\Layout\HandlebarsLayoutAction;

/**
 * BlockHelper
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 * @see https://github.com/shannonmoeller/handlebars-layouts#block-name
 */
class BlockHelper implements HelperInterface
{
    /**
     * @param string $name
     * @param array<string, mixed> $options
     * @return string
     */
    public function evaluate(string $name, array $options): string
    {
        $data = $options['_this'];
        $actions = $data['_layoutActions'] ?? [];
        $stack = $data['_layoutStack'] ?? [];

        // Parse layouts and fetch all parsed layout actions for the requested block
        while (!empty($stack)) {
            /** @var HandlebarsLayout $layout */
            $layout = array_shift($stack);
            if (!$layout->isParsed()) {
                $layout->parse();
            }
            $actions = array_merge($actions, $layout->getActions($name));
        }

        // Walk through layout actions and apply them to the rendered block
        $fn = $options['fn'] ?? function () {
            return '';
        };

        return array_reduce($actions, function (string $value, HandlebarsLayoutAction $action) {
            return $action->render($value);
        }, $fn($options, ['data' => $data]));
    }
}
