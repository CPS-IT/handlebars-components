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

use Cpsit\Typo3HandlebarsComponents\Domain\Model\Dto\Link;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * LinkTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class LinkTest extends UnitTestCase
{
    protected Link $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new Link('foo', 'baz');
    }

    /**
     * @test
     */
    public function getUrlReturnsUrl(): void
    {
        self::assertSame('foo', $this->subject->getUrl());
    }

    /**
     * @test
     */
    public function getLabelReturnsLabel(): void
    {
        self::assertSame('baz', $this->subject->getLabel());
    }

    /**
     * @test
     */
    public function getTargetReturnsTarget(): void
    {
        self::assertNull($this->subject->getTarget());
        self::assertSame('_blank', (new Link('foo', 'baz', '_blank'))->getTarget());
    }
}
