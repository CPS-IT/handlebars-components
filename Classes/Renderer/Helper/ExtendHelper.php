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

namespace Cpsit\Typo3HandlebarsComponents\Renderer\Helper;

use Fr\Typo3Handlebars\Renderer\Helper\HelperInterface;
use Fr\Typo3Handlebars\Renderer\RendererInterface;
use Fr\Typo3HandlebarsComponents\Renderer\Component\Layout\HandlebarsLayout;

/**
 * ExtendHelper
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 * @see https://github.com/shannonmoeller/handlebars-layouts#extend-partial-context-keyvalue-
 */
class ExtendHelper implements HelperInterface
{
    /**
     * @var RendererInterface
     */
    protected $renderer;

    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function evaluate(string $name): string
    {
        // Get helper options
        $arguments = \func_get_args();
        array_shift($arguments);
        $options = array_pop($arguments);

        // Custom context is optional
        $customContext = [];
        if ($arguments !== []) {
            $customContext = (array)array_pop($arguments);
        }

        // Create new handlebars layout item
        $fn = \is_callable($options['fn'] ?? '') ? $options['fn'] : static fn (): string => '';
        $handlebarsLayout = new HandlebarsLayout($fn);

        // Add layout to layout stack
        $data = &$options['_this'];
        if (!isset($data['_layoutStack'])) {
            $data['_layoutStack'] = [];
        }
        $data['_layoutStack'][] = $handlebarsLayout;

        // Merge data with supplied data
        $renderData = array_replace_recursive($options['_this'], $customContext, $options['hash']);

        // Render layout with merged data
        return $this->renderer->render($name, $renderData);
    }
}
