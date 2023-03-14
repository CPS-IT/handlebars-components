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

namespace Fr\Typo3HandlebarsComponents\DependencyInjection;

use Fr\Typo3Handlebars\Renderer\Helper\HelperInterface;
use Fr\Typo3HandlebarsComponents\Configuration\Extension;
use Fr\Typo3HandlebarsComponents\Renderer\Helper\BlockHelper;
use Fr\Typo3HandlebarsComponents\Renderer\Helper\ContentHelper;
use Fr\Typo3HandlebarsComponents\Renderer\Helper\ExtendHelper;
use Fr\Typo3HandlebarsComponents\Renderer\Helper\RenderHelper;
use Fr\Typo3HandlebarsComponents\Renderer\Template\FlatTemplateResolver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Exception;

/**
 * FeatureRegistrationPass
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 * @internal
 */
final class FeatureRegistrationPass implements CompilerPassInterface
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var ExtensionConfiguration
     */
    private $extensionConfiguration;

    public function process(ContainerBuilder $container): void
    {
        $this->container = $container;
        $this->extensionConfiguration = $this->container->get(ExtensionConfiguration::class);

        if ($this->isFeatureEnabled('blockHelper')) {
            $this->activateHelper('block', BlockHelper::class);
        }
        if ($this->isFeatureEnabled('contentHelper')) {
            $this->activateHelper('content', ContentHelper::class);
        }
        if ($this->isFeatureEnabled('extendHelper')) {
            $this->activateHelper('extend', ExtendHelper::class);
        }
        if ($this->isFeatureEnabled('renderHelper')) {
            $this->activateHelper('render', RenderHelper::class);
        }
        if ($this->isFeatureEnabled('flatTemplateResolver')) {
            $this->activateFlatTemplateResolver();
        }
    }

    /**
     * @param class-string<HelperInterface> $className
     */
    private function activateHelper(string $name, string $className, string $methodName = 'evaluate'): void
    {
        $definition = $this->container->getDefinition($className);
        $definition->addTag('handlebars.helper', [
            'identifier' => $name,
            'method' => $methodName,
        ]);
    }

    private function activateFlatTemplateResolver(): void
    {
        $this->container->getDefinition('handlebars.template_resolver')->setClass(FlatTemplateResolver::class);
        $this->container->getDefinition('handlebars.partial_resolver')->setClass(FlatTemplateResolver::class);
    }

    private function isFeatureEnabled(string $featureName): bool
    {
        $configurationPath = sprintf('features/%s/enable', $featureName);

        try {
            return (bool)$this->extensionConfiguration->get(Extension::KEY, $configurationPath);
        } catch (Exception $e) {
            return false;
        }
    }
}
