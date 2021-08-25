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
use Fr\Typo3Handlebars\Renderer\RendererInterface;
use LightnCandy\SafeString;
use TYPO3\CMS\Core\Utility\ArrayUtility;

/**
 * RenderHelper
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 * @see https://github.com/frctl/fractal/blob/main/packages/handlebars/src/helpers/render.js
 */
class RenderHelper implements HelperInterface
{
    /**
     * @var RendererInterface
     */
    protected $renderer;

    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function evaluate(string $name): SafeString
    {
        // Get helper options
        $arguments = func_get_args();
        array_shift($arguments);
        $options = array_pop($arguments);

        // Resolve data
        $rootData = $options['data']['root'];
        $merge = (bool)($options['hash']['merge'] ?? false);

        // Fetch custom context
        // ====================
        // Custom contexts can be defined as helper argument, e.g.
        // {{render '@foo' customContext}}
        $context = reset($arguments);
        if (!is_array($context)) {
            $context = [];
        }

        // Fetch default context
        // =====================
        // Default contexts can be defined by using the template name when rendering a
        // specific template, e.g. if $name = '@foo' then $rootData['@foo'] is requested
        $defaultContext = $rootData[$name] ?? [];

        // Resolve context
        // ===============
        // Use default context as new context if no custom context is given, otherwise
        // merge both contexts in case merge=true is passed as helper option, e.g.
        // {{render '@foo' customContext merge=true}}
        if ([] === $context) {
            $context = $defaultContext;
        } elseif ($merge) {
            ArrayUtility::mergeRecursiveWithOverrule($defaultContext, $context);
            $context = $defaultContext;
        }

        return new SafeString($this->renderer->render($name, $context));
    }
}
