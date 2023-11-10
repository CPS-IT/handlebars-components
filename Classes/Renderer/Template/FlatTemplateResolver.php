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
use Fr\Typo3Handlebars\Renderer\Template\TemplatePaths;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * FlatTemplateResolver
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 * @see https://fractal.build/guide/core-concepts/naming.html
 */
class FlatTemplateResolver extends HandlebarsTemplateResolver
{
    protected const VARIANT_SEPARATOR = '--';

    /**
     * @var array<string, SplFileInfo>
     */
    protected $flattenedTemplates = [];

    /**
     * @var int
     */
    protected $depth = 30;

    public function __construct(TemplatePaths $templateRootPaths, array $supportedFileExtensions = self::DEFAULT_FILE_EXTENSIONS)
    {
        parent::__construct($templateRootPaths, $supportedFileExtensions);
        $this->buildTemplateMap();
    }

    public function resolveTemplatePath(string $templatePath): string
    {
        // Use default path resolving if path is not prefixed by "@"
        if (!str_starts_with($templatePath, '@')) {
            return parent::resolveTemplatePath($templatePath);
        }

        // Strip "@" prefix from given template path
        $templateName = ltrim($templatePath, '@');

        // Strip off template variant
        if (str_contains($templateName, self::VARIANT_SEPARATOR)) {
            [$templateName] = explode(self::VARIANT_SEPARATOR, $templateName, 2);
        }

        if (isset($this->flattenedTemplates[$templateName])) {
            return $this->flattenedTemplates[$templateName]->getPathname();
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

        // Explicitly sort files and directories by name in order to streamline ordering
        // with logic used in Fractal to ensure that the first occurrence of a flattened
        // file is always used instead of relying on random behavior,
        // see https://fractal.build/guide/core-concepts/naming.html#uniqueness
        $finder->sortByName();

        // Build template map
        foreach ($this->templateRootPaths as $templateRootPath) {
            $path = $this->resolveFilename($templateRootPath);
            $pathFinder = clone $finder;
            $pathFinder->in($path);

            foreach ($pathFinder as $file) {
                if ($this->isFirstOccurrenceInTemplateRoot($file)) {
                    $this->registerTemplate($file);
                }
            }
        }
    }

    protected function isFirstOccurrenceInTemplateRoot(SplFileInfo $file): bool
    {
        $filename = $this->resolveFlatFilename($file);

        // Early return if template is not registered yet
        if (!isset($this->flattenedTemplates[$filename])) {
            return true;
        }

        // In order to streamline template file flattening with logic used in Fractal,
        // we always use the first flat file occurrence as resolved template, but provide
        // the option to override exactly this file within other template root paths.
        return $this->flattenedTemplates[$filename]->getRelativePathname() === $file->getRelativePathname();
    }

    protected function registerTemplate(SplFileInfo $file): void
    {
        $this->flattenedTemplates[$this->resolveFlatFilename($file)] = $file;
    }

    protected function resolveFlatFilename(SplFileInfo $file): string
    {
        return pathinfo($file->getPathname(), PATHINFO_FILENAME);
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
