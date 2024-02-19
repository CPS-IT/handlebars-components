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

namespace Cpsit\Typo3HandlebarsComponents\Tests\Unit\DependencyInjection;

use Fr\Typo3Handlebars\DependencyInjection\Extension\HandlebarsExtension;
use Fr\Typo3Handlebars\Renderer\Helper\HelperInterface;
use Fr\Typo3Handlebars\Renderer\RendererInterface;
use Fr\Typo3Handlebars\Renderer\Template\TemplatePaths;
use Fr\Typo3HandlebarsComponents\DependencyInjection\FeatureRegistrationPass;
use Fr\Typo3HandlebarsComponents\Renderer\Helper\BlockHelper;
use Fr\Typo3HandlebarsComponents\Renderer\Helper\ContentHelper;
use Fr\Typo3HandlebarsComponents\Renderer\Helper\ExtendHelper;
use Fr\Typo3HandlebarsComponents\Renderer\Helper\RenderHelper;
use Fr\Typo3HandlebarsComponents\Renderer\Template\FlatTemplateResolver;
use Fr\Typo3HandlebarsComponents\Tests\Functional\Fixtures\DummyExtensionConfiguration;
use Fr\Typo3HandlebarsComponents\Tests\Functional\Fixtures\DummyExtensionConfigurationV10;
use Fr\Typo3HandlebarsComponents\Tests\Unit\Fixtures\DummyConfigurationManager;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\DependencyInjection\PublicServicePass;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * FeatureRegistrationPassTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class FeatureRegistrationPassTest extends UnitTestCase
{
    /**
     * @var array<string, bool>
     */
    protected array $activatedFeatures = [
        'blockHelper' => false,
        'contentHelper' => false,
        'extendHelper' => false,
        'renderHelper' => false,
        'flatTemplateResolver' => false,
    ];

    /**
     * @test
     */
    public function processDoesNotActivateDisabledFeatures(): void
    {
        $container = $this->buildContainer();

        self::assertSame([], $container->findTaggedServiceIds('handlebars.helper'));
        self::assertNotInstanceOf(FlatTemplateResolver::class, $container->get('handlebars.template_resolver'));
        self::assertNotInstanceOf(FlatTemplateResolver::class, $container->get('handlebars.partial_resolver'));
    }

    /**
     * @test
     */
    public function processActivatesEnabledHelpers(): void
    {
        $this->activatedFeatures['blockHelper'] = true;
        $this->activatedFeatures['contentHelper'] = true;
        $this->activatedFeatures['extendHelper'] = true;
        $this->activatedFeatures['renderHelper'] = true;

        $container = $this->buildContainer();

        self::assertCount(4, $container->findTaggedServiceIds('handlebars.helper'));
        self::assertHelperIsTagged($container, BlockHelper::class, 'block');
        self::assertHelperIsTagged($container, ContentHelper::class, 'content');
        self::assertHelperIsTagged($container, ExtendHelper::class, 'extend');
        self::assertHelperIsTagged($container, RenderHelper::class, 'render');
    }

    /**
     * @test
     */
    public function processActivatesEnabledTemplateResolvers(): void
    {
        $this->activatedFeatures['flatTemplateResolver'] = true;

        $container = $this->buildContainer();

        self::assertSame([], $container->findTaggedServiceIds('handlebars.helper'));
        self::assertInstanceOf(FlatTemplateResolver::class, $container->get('handlebars.template_resolver'));
        self::assertInstanceOf(FlatTemplateResolver::class, $container->get('handlebars.partial_resolver'));
    }

    /**
     * @test
     */
    public function processDoesNotActivateFeaturesIfExtensionConfigurationIsMissing(): void
    {
        unset($this->activatedFeatures['blockHelper']);
        unset($this->activatedFeatures['contentHelper']);
        unset($this->activatedFeatures['extendHelper']);
        unset($this->activatedFeatures['renderHelper']);
        unset($this->activatedFeatures['flatTemplateResolver']);

        $container = $this->buildContainer();

        self::assertSame([], $container->findTaggedServiceIds('handlebars.helper'));
        self::assertNotInstanceOf(FlatTemplateResolver::class, $container->get('handlebars.template_resolver'));
        self::assertNotInstanceOf(FlatTemplateResolver::class, $container->get('handlebars.partial_resolver'));
    }

    /**
     * @param class-string<HelperInterface> $className
     */
    private static function assertHelperIsTagged(ContainerBuilder $container, string $className, string $name): void
    {
        $serviceIds = $container->findTaggedServiceIds('handlebars.helper');
        $expectedConfiguration = [
            'identifier' => $name,
            'method' => 'evaluate',
        ];

        self::assertArrayHasKey($className, $serviceIds);
        self::assertSame($expectedConfiguration, $serviceIds[$className][0]);
    }

    private function buildContainer(): ContainerBuilder
    {
        $container = new ContainerBuilder();

        $packagePath = \dirname(__DIR__, 3);
        $yamlFileLoader = new YamlFileLoader($container, new FileLocator($packagePath . '/Configuration'));
        $yamlFileLoader->load('Services.yaml');

        // Constructor arguments of RenderHelper
        $container->register(TypoScriptService::class);
        $container->register(ContentObjectRenderer::class);

        // Provide dummy extension configuration class
        $dummyExtensionConfiguration = (new Typo3Version())->getMajorVersion() < 10
            ? new DummyExtensionConfigurationV10($this->activatedFeatures)
            : new DummyExtensionConfiguration($this->activatedFeatures)
        ;
        $container->set(ExtensionConfiguration::class, $dummyExtensionConfiguration);

        // Simulate required services
        $dummyTemplatePathsDefinition = new Definition(TemplatePaths::class);
        $dummyTemplatePathsDefinition->addArgument(new DummyConfigurationManager());
        $dummyTemplatePathsDefinition->addMethodCall('setContainer', [$container]);
        $dummyTemplateResolverDefinition = (new Definition('stdClass'))->setPublic(true);
        $dummyTemplateResolverDefinition->addArgument(new Reference(TemplatePaths::class));

        $container->setDefinition(TemplatePaths::class, $dummyTemplatePathsDefinition);
        $container->setDefinition('handlebars.template_resolver', $dummyTemplateResolverDefinition);
        $container->setDefinition('handlebars.partial_resolver', $dummyTemplateResolverDefinition);
        $container->setDefinition(RendererInterface::class, $dummyTemplateResolverDefinition);

        $container->setParameter(HandlebarsExtension::PARAMETER_TEMPLATE_ROOT_PATHS, []);
        $container->setParameter(HandlebarsExtension::PARAMETER_PARTIAL_ROOT_PATHS, []);

        $container->addCompilerPass(new FeatureRegistrationPass());
        $container->addCompilerPass(new PublicServicePass('handlebars.helper'));
        $container->compile();

        return $container;
    }
}
