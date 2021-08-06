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

namespace Fr\Typo3HandlebarsComponents\Renderer\Template;

use Fr\Typo3Handlebars\Exception\TemplateNotFoundException;
use Fr\Typo3Handlebars\Renderer\Template\HandlebarsTemplateResolver;
use Symfony\Component\Finder\Finder;
use TYPO3\CMS\Core\Utility\StringUtility;

/**
 * FlatTemplateResolver
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class FlatTemplateResolver extends HandlebarsTemplateResolver
{
    /**
     * @var array<string, string>
     */
    protected $flattenedTemplates = [];

    /**
     * @var int
     */
    protected $depth = 30;

    public function __construct(array $templateRootPaths, array $supportedFileExtensions = self::DEFAULT_FILE_EXTENSIONS)
    {
        parent::__construct($templateRootPaths, $supportedFileExtensions);
        $this->buildTemplateMap();
    }

    public function resolveTemplatePath(string $templatePath): string
    {
        // Use default path resolving if path is not prefixed by "@"
        if (!StringUtility::beginsWith($templatePath, '@')) {
            return parent::resolveTemplatePath($templatePath);
        }

        // Strip "@" prefix from given template path
        $templateName = ltrim($templatePath, '@');

        if (isset($this->flattenedTemplates[$templateName])) {
            return $this->flattenedTemplates[$templateName];
        }

        throw new TemplateNotFoundException($templateName, 1628256108);
    }

    protected function buildTemplateMap(): void
    {
        // Reset flattened templates
        $this->flattenedTemplates = [];

        // Instantiate finder
        $finder = new Finder();
        $finder->files();
        $finder->name([...$this->buildExtensionPatterns()]);
        $finder->depth(sprintf('< %d', $this->depth));

        // Build template map
        foreach ($this->templateRootPaths as $templateRootPath) {
            $path = $this->resolveFilename($templateRootPath);
            $pathFinder = clone $finder;
            $pathFinder->in($path);

            foreach ($pathFinder as $file) {
                $pathname = $file->getPathname();
                $filename = pathinfo($pathname, PATHINFO_FILENAME);
                $this->flattenedTemplates[$filename] = $pathname;
            }
        }
    }

    /**
     * @return \Generator<string>
     */
    protected function buildExtensionPatterns(): \Generator
    {
        foreach ($this->supportedFileExtensions as $extension) {
            yield sprintf('*.%s', $extension);
        }
    }
}
