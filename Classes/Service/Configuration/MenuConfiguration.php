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

namespace Fr\Typo3HandlebarsComponents\Service\Configuration;

use Fr\Typo3HandlebarsComponents\Exception\InvalidConfigurationException;
use Fr\Typo3HandlebarsComponents\Utility\TypoScriptUtility;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * MenuConfiguration
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class MenuConfiguration
{
    public const DEFAULT = 'default';
    public const LANGUAGE = 'language';
    public const CUSTOM = 'custom';

    /**
     * @var array<string, mixed>
     */
    protected $typoScriptConfiguration;

    /**
     * @var string
     */
    protected $type;

    /**
     * @param array<string, mixed> $typoScriptConfiguration
     * @param string $type
     */
    public function __construct(array $typoScriptConfiguration, string $type = self::DEFAULT)
    {
        $this->typoScriptConfiguration = $typoScriptConfiguration;
        $this->type = $type;
        $this->validate();
    }

    public static function directory(int $rootPageId = null, int $levels = 1): self
    {
        return new self([
            'special' => 'directory',
            'special.' => [
                'value' => $rootPageId ?? 'auto',
            ],
            'levels' => $levels,
        ]);
    }

    /**
     * @param int[] $pageIds
     * @return self
     */
    public static function list(array $pageIds): self
    {
        $pageIds = array_filter($pageIds, 'is_int');

        return new self([
            'special' => 'list',
            'special.' => [
                'value' => implode(',', $pageIds),
            ],
        ]);
    }

    public static function rootline(int $begin = 1, int $end = -1): self
    {
        return new self([
            'special' => 'rootline',
            'special.' => [
                'range' => sprintf('%d|%d', $begin, $end),
            ],
        ]);
    }

    /**
     * @param int[] $languages
     * @param string[] $excludedParameters
     * @return self
     */
    public static function language(array $languages = [], array $excludedParameters = []): self
    {
        $languages = array_filter($languages, 'is_int');
        $excludedParameters = array_filter($excludedParameters, 'is_string');

        return new self([
            'languages' => [] !== $languages ? implode(',', $languages) : 'auto',
            'addQueryString.' => [
                'exclude' => implode(',', $excludedParameters),
            ],
        ], self::LANGUAGE);
    }

    /**
     * @param class-string<DataProcessorInterface> $dataProcessor
     * @param array<string, mixed> $configuration
     * @return self
     */
    public static function custom(string $dataProcessor, array $configuration = []): self
    {
        return new self([
            'dataProcessing.' => [
                '10' => $dataProcessor,
                '10.' => $configuration,
            ],
        ], self::CUSTOM);
    }

    /**
     * @return array<string, mixed>
     */
    public function getTypoScriptConfiguration(): array
    {
        return $this->typoScriptConfiguration;
    }

    /**
     * @param string $path
     * @param mixed $value
     * @return self
     */
    public function addTypoScriptConfiguration(string $path, $value): self
    {
        // TypoScript paths always contain a trailing dot for array values
        if (is_array($value)) {
            $path = rtrim($path, '.') . '.';
        }

        ArrayUtility::mergeRecursiveWithOverrule(
            $this->typoScriptConfiguration,
            TypoScriptUtility::buildTypoScriptArrayFromPath($path, $value)
        );
        $this->validate();

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @throws InvalidConfigurationException
     */
    protected function validate(): void
    {
        if (self::CUSTOM === $this->type && !isset($this->typoScriptConfiguration['dataProcessing.'])) {
            throw InvalidConfigurationException::create('dataProcessing.');
        }

        TypoScriptUtility::validateTypoScriptArray($this->typoScriptConfiguration);
    }
}
