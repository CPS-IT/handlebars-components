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
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * ContentHelper
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 * @see https://github.com/shannonmoeller/handlebars-layouts#content-name-modeappendprependreplace
 */
class ContentHelper implements HelperInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @param string $name
     * @param array<string, mixed> $context
     * @return string|bool
     */
    public function evaluate(string $name, array $context)
    {
        $data = $context['_this'];
        $mode = $context['hash']['mode'] ?? HandlebarsLayoutAction::REPLACE;
        /** @var HandlebarsLayout[] $layoutStack */
        $layoutStack = $data['_layoutStack'] ?? null;

        // Early return if "content" helper is requested outside of an "extend" helper block
        if (empty($data['_layoutStack'])) {
            $this->logger->error('Handlebars layout helper "content" can only be used within an "extend" helper block!', ['name' => $name]);
            return '';
        }

        // Get upper layout from stack
        $layout = end($layoutStack);

        // Usage in conditional context: Test whether given required block is registered
        if (!is_callable($context['fn'] ?? '')) {
            return $layout->hasAction($name);
        }

        // Add concrete action for the requested block
        $action = new HandlebarsLayoutAction($data, $context['fn'], $mode);
        $layout->addAction($name, $action);

        // This helper does not return any content, it's just here to register layout actions
        return '';
    }
}
