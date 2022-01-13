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

use Fr\Typo3Handlebars\DataProcessing\DataProcessorInterface;
use Fr\Typo3Handlebars\Renderer\Helper\HelperInterface;
use Fr\Typo3Handlebars\Renderer\RendererInterface;
use Fr\Typo3HandlebarsComponents\Exception\InvalidConfigurationException;
use LightnCandy\SafeString;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

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

    /**
     * @var TypoScriptService
     */
    protected $typoScriptService;

    /**
     * @var ContentObjectRenderer
     */
    protected ContentObjectRenderer $contentObjectRenderer;

    public function __construct(
        RendererInterface $renderer,
        TypoScriptService $typoScriptService,
        ContentObjectRenderer $contentObjectRenderer
    ) {
        $this->renderer = $renderer;
        $this->typoScriptService = $typoScriptService;
        $this->contentObjectRenderer = $contentObjectRenderer;
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
        $renderUncached = (bool)($options['hash']['uncached'] ?? false);

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

        if ($renderUncached) {
            $content = $this->registerUncachedTemplateBlock($name, $context);
        } else {
            $content = $this->renderer->render($name, $context);
        }

        return new SafeString($content);
    }

    /**
     * @param string $templateName
     * @param array<string, mixed> $context
     * @return string
     */
    protected function registerUncachedTemplateBlock(string $templateName, array $context): string
    {
        $processorClass = $context['_processor'] ?? null;

        // Check whether the required data processor is valid
        if (!is_string($processorClass) || !in_array(DataProcessorInterface::class, class_implements($processorClass))) {
            throw InvalidConfigurationException::create('_processor');
        }

        // Do not pass data processor reference as context to requested data processor
        unset($context['_processor']);

        return $this->contentObjectRenderer->cObjGetSingle('USER_INT', [
            'userFunc' => $processorClass . '->process',
            'userFunc.' => [
                'templatePath' => $templateName,
                'context.' => $this->typoScriptService->convertPlainArrayToTypoScriptArray($context),
            ],
        ]);
    }
}
