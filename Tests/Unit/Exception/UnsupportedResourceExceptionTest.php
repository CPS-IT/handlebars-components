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

namespace Fr\Typo3HandlebarsComponents\Tests\Unit\Exception;

use Fr\Typo3HandlebarsComponents\Domain\Model\Media\Media;
use Fr\Typo3HandlebarsComponents\Exception\UnsupportedResourceException;
use Fr\Typo3HandlebarsComponents\Tests\Unit\Fixtures\DummyResourceStorage;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * UnsupportedResourceExceptionTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class UnsupportedResourceExceptionTest extends UnitTestCase
{
    /**
     * @var File
     */
    protected $file;

    protected function setUp(): void
    {
        parent::setUp();
        $this->file = new File(['identifier' => 'foo.baz', 'name' => 'foo.baz'], new DummyResourceStorage());
    }

    /**
     * @test
     */
    public function forFileReturnsExceptionForGivenFile(): void
    {
        $actual = UnsupportedResourceException::forFile($this->file);

        self::assertInstanceOf(UnsupportedResourceException::class, $actual);
        self::assertSame('The file "foo.baz" is not supported.', $actual->getMessage());
        self::assertSame(1633012917, $actual->getCode());
    }

    /**
     * @test
     */
    public function forMediaReturnsExceptionForGivenMedia(): void
    {
        $media = new Media($this->file);
        $actual = UnsupportedResourceException::forMedia($media);

        self::assertInstanceOf(UnsupportedResourceException::class, $actual);
        self::assertSame('The media "foo" is not supported.', $actual->getMessage());
        self::assertSame(1633015669, $actual->getCode());
    }
}
