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

namespace Fr\Typo3HandlebarsComponents\Tests\Unit\DependencyInjection;

use Fr\Typo3Handlebars\Renderer\Helper\HelperInterface;
use Fr\Typo3Handlebars\Renderer\RendererInterface;
use Fr\Typo3HandlebarsComponents\DependencyInjection\FeatureRegistrationPass;
use Fr\Typo3HandlebarsComponents\Renderer\Helper\BlockHelper;
use Fr\Typo3HandlebarsComponents\Renderer\Helper\ContentHelper;
use Fr\Typo3HandlebarsComponents\Renderer\Helper\ExtendHelper;
use Fr\Typo3HandlebarsComponents\Renderer\Helper\RenderHelper;
use Fr\Typo3HandlebarsComponents\Renderer\Template\FlatTemplateResolver;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\DependencyInjection\PublicServicePass;
use TYPO3\CMS\Core\Exception;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * FeatureRegistrationPassTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class FeatureRegistrationPassTest extends UnitTestCase
{
    /**
     * @var array<string, bool>
     */
    protected $activatedFeatures = [
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
     * @param ContainerBuilder $container
     * @param class-string<HelperInterface> $className
     * @param string $name
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

        $packagePath = dirname(__DIR__, 3);
        $yamlFileLoader = new YamlFileLoader($container, new FileLocator($packagePath . '/Configuration'));
        $yamlFileLoader->load('Services.yaml');

        // Provide dummy extension configuration class
        $dummyExtensionConfiguration = new class($this->activatedFeatures) {
            /**
             * @var array<string, bool>
             */
            private $activatedFeatures;

            /**
             * @param array<string, bool> $activatedFeatures
             */
            public function __construct(array $activatedFeatures)
            {
                $this->activatedFeatures = $activatedFeatures;
            }

            public function get(/** @noinspection PhpUnusedParameterInspection */ string $extensionKey, string $path): bool
            {
                [, $featureName] = explode('/', $path);

                if (!isset($this->activatedFeatures[$featureName])) {
                    throw new Exception('dummy exception');
                }

                return $this->activatedFeatures[$featureName];
            }
        };
        $container->set(ExtensionConfiguration::class, $dummyExtensionConfiguration);

        // Simulate required services
        $dummyDefinition = (new Definition('stdClass'))->setPublic(true);
        $container->setDefinition('handlebars.template_resolver', $dummyDefinition->addArgument([]));
        $container->setDefinition('handlebars.partial_resolver', $dummyDefinition->addArgument([]));
        $container->setDefinition(RendererInterface::class, $dummyDefinition);

        $container->addCompilerPass(new FeatureRegistrationPass());
        $container->addCompilerPass(new PublicServicePass('handlebars.helper'));
        $container->compile();

        return $container;
    }
}
