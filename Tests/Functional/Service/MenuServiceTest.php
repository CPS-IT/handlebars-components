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

namespace Fr\Typo3HandlebarsComponents\Tests\Functional\Service;

use Fr\Typo3HandlebarsComponents\Service\Configuration\MenuConfiguration;
use Fr\Typo3HandlebarsComponents\Service\MenuService;
use Fr\Typo3HandlebarsComponentsTestExtension\DummyMenuProcessor;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentDataProcessor;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * MenuServiceTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class MenuServiceTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = [
        'typo3conf/ext/handlebars_components/Tests/Functional/Fixtures/test_extension',
    ];

    protected MenuService $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new MenuService(new ContentObjectRenderer(), new ContentDataProcessor(GeneralUtility::getContainer()));

        $this->importDataSet(\dirname(__DIR__) . '/Fixtures/pages.xml');
        $this->importDataSet(\dirname(__DIR__) . '/Fixtures/sys_template.xml');

        $this->setUpFrontendRootPage(1);
        $this->setUpFrontendSite(1);
    }

    /**
     * @test
     * @dataProvider buildMenuReturnsProcessedMenuDataProvider
     * @param string[] $expectedMenuItemLinks
     */
    public function buildMenuReturnsProcessedMenu(MenuConfiguration $configuration, int $pageId, array $expectedMenuItemLinks): void
    {
        $this->prepareTypoScriptForMenuConfiguration($configuration);

        $request = (new InternalRequest())->withPageId($pageId);
        // @todo Migrate to executeFrontendSubRequest() once support for TYPO3 v10 is dropped
        $response = $this->executeFrontendRequest($request);
        $body = (string)$response->getBody();

        self::assertJson($body, 'Received invalid JSON from internal sub-request');

        $jsonArray = json_decode($body, true);

        self::assertCount(\count($expectedMenuItemLinks), $jsonArray);

        for ($i = 0; $i < \count($expectedMenuItemLinks); $i++) {
            self::assertSame($expectedMenuItemLinks[$i], $jsonArray[$i]);
        }
    }

    /**
     * @return \Generator<string, array{MenuConfiguration, int, array<int, array<string, mixed>>}>
     */
    public function buildMenuReturnsProcessedMenuDataProvider(): \Generator
    {
        $slugs = [
            1 => '/page-1',
            2 => '/page-1/page-2',
            3 => '/page-1/page-3',
            4 => '/de/page-1',
        ];
        $page = static fn (int $pageId, bool $active = false, bool $current = false, array $subItems = []): array => [
            'link' => $slugs[$pageId],
            'active' => $active,
            'current' => $current,
            'subItems' => $subItems,
        ];

        yield 'menu with special=directory' => [
            MenuConfiguration::directory(1),
            1,
            [
                $page(2),
                $page(3),
            ],
        ];
        yield 'menu with special=list' => [
            MenuConfiguration::list([1, 2, 3]),
            1,
            [
                $page(1, true, true),
                $page(2),
                $page(3),
            ],
        ];
        yield 'menu with special=rootline' => [
            MenuConfiguration::rootline(),
            2,
            [
                $page(1, true),
                $page(2, true, true),
            ],
        ];
        yield 'language menu' => [
            MenuConfiguration::language([0, 1]),
            1,
            [
                $page(1, true),
                $page(4, false),
            ],
        ];
        yield 'custom menu' => [
            MenuConfiguration::custom(DummyMenuProcessor::class),
            1,
            [
                ['link' => 'https://www.example.com', 'active' => false, 'current' => false, 'subItems' => []],
                ['link' => 'https://www.example.com/de', 'active' => false, 'current' => false, 'subItems' => []],
                ['link' => 'https://www.example.com/es', 'active' => false, 'current' => false, 'subItems' => []],
            ],
        ];
    }

    private function prepareTypoScriptForMenuConfiguration(MenuConfiguration $configuration): void
    {
        $this->addTypoScriptToTemplateRecord(1, '
config.disableAllHeaderCode = 1
page = PAGE
page.10 = USER
page.10.userFunc = Fr\Typo3HandlebarsComponentsTestExtension\DummyMenuProcessor->preProcess
page.10.userFunc.menuConfiguration = ' . json_encode($configuration->getTypoScriptConfiguration()) . '
page.10.userFunc.menuType = ' . $configuration->getType() . '
        ');
    }

    private function setUpFrontendSite(int $pageId): void
    {
        $configuration = [
            'rootPageId' => $pageId,
            'base' => '/',
            'websiteTitle' => '',
            'languages' => [
                [
                    'title' => 'English',
                    'enabled' => true,
                    'languageId' => '0',
                    'base' => '/',
                    'typo3Language' => 'default',
                    'locale' => 'en_US.UTF-8',
                    'iso-639-1' => 'en',
                    'websiteTitle' => 'Site EN',
                    'navigationTitle' => '',
                    'hreflang' => '',
                    'direction' => '',
                    'flag' => 'us',
                ],
                [
                    'title' => 'German',
                    'enabled' => true,
                    'languageId' => '1',
                    'base' => '/de/',
                    'typo3Language' => 'de',
                    'locale' => 'de_DE.UTF-8',
                    'iso-639-1' => 'de',
                    'websiteTitle' => 'Site DE',
                    'navigationTitle' => '',
                    'hreflang' => '',
                    'direction' => '',
                    'flag' => 'de',
                ],
            ],
            'errorHandling' => [],
            'routes' => [],
        ];
        GeneralUtility::mkdir_deep($this->instancePath . '/typo3conf/sites/testing/');
        $yamlFileContents = Yaml::dump($configuration, 99, 2);
        $fileName = $this->instancePath . '/typo3conf/sites/testing/config.yaml';
        GeneralUtility::writeFile($fileName, $yamlFileContents);
    }
}
