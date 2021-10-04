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

namespace Fr\Typo3HandlebarsComponents\Tests\Unit\Domain\Model\Media;

use Fr\Typo3HandlebarsComponents\Domain\Model\Media\OnlineMedia;
use Fr\Typo3HandlebarsComponents\Tests\Unit\Fixtures\DummyResourceStorage;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * OnlineMediaTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class OnlineMediaTest extends UnitTestCase
{
    /**
     * @var File
     */
    protected $file;

    /**
     * @var OnlineMedia
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->file = new File(['identifier' => 'foo.baz', 'name' => 'foo.baz'], new DummyResourceStorage());
        $this->subject = new OnlineMedia($this->file, '12345', 'https://example.com/foo.baz');
    }

    /**
     * @test
     */
    public function getOnlineMediaIdReturnsOnlineMediaId(): void
    {
        self::assertSame('12345', $this->subject->getOnlineMediaId());
    }

    /**
     * @test
     */
    public function getPublicUrlReturnsPublicUrl(): void
    {
        self::assertSame('https://example.com/foo.baz', $this->subject->getPublicUrl());
    }

    /**
     * @test
     */
    public function getPreviewImageReturnsPreviewImage(): void
    {
        self::assertNull($this->subject->getPreviewImage());
        self::assertSame('foo', $this->subject->setPreviewImage('foo')->getPreviewImage());
    }
}
