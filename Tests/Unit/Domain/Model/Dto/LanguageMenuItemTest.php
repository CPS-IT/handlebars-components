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

namespace Cpsit\Typo3HandlebarsComponents\Tests\Unit\Domain\Model\Dto;

use Cpsit\Typo3HandlebarsComponents\Domain\Model\Dto\LanguageMenuItem;
use Cpsit\Typo3HandlebarsComponents\Domain\Model\Dto\Link;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * LanguageMenuItemTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class LanguageMenuItemTest extends UnitTestCase
{
    protected SiteLanguage $siteLanguage;
    protected LanguageMenuItem $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->siteLanguage = new SiteLanguage(1, 'en_US', new Uri('https://www.example.com'), []);
        $this->subject = new LanguageMenuItem(new Link('foo', 'baz'), $this->siteLanguage);
    }

    /**
     * @test
     */
    public function getSiteLanguageReturnsSiteLanguage(): void
    {
        self::assertSame($this->siteLanguage, $this->subject->getSiteLanguage());
    }

    /**
     * @test
     */
    public function setSiteLanguageAppliesGivenSiteLanguage(): void
    {
        $siteLanguage = clone $this->siteLanguage;

        self::assertSame($siteLanguage, $this->subject->setSiteLanguage($siteLanguage)->getSiteLanguage());
    }

    /**
     * @test
     */
    public function isAvailableTestsWhetherSubjectIsAvailable(): void
    {
        self::assertFalse($this->subject->isAvailable());
        self::assertTrue($this->subject->setAvailable(true)->isAvailable());
    }
}
