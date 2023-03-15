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

use Fr\Typo3HandlebarsComponents\Domain\Model\Media\Media;
use Fr\Typo3HandlebarsComponents\Tests\Unit\Fixtures\DummyResourceStorage;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\MetaDataAspect;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * MediaTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class MediaTest extends UnitTestCase
{
    protected File $file;
    protected Media $subject;
    protected ?MetaDataAspect $metaDataAspect = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->file = new File([
            'identifier' => 'foo.baz',
            'name' => 'foo.baz',
            'alternative' => null,
            'title' => null,
        ], new DummyResourceStorage());
        $this->subject = new Media($this->file);
    }

    /**
     * @test
     */
    public function getOriginalFileReturnsOriginalFile(): void
    {
        self::assertSame($this->file, $this->subject->getOriginalFile());
    }

    /**
     * @test
     */
    public function getExtensionReturnsFileExtension(): void
    {
        self::assertSame('baz', $this->subject->getExtension());
    }

    /**
     * @test
     * @dataProvider getAlternativeReturnsAlternativeTextDataProvider
     * @param array<string, string|null> $additionalProperties
     */
    public function getAlternativeReturnsAlternativeText(array $additionalProperties, string $expected): void
    {
        $this->file->updateProperties($additionalProperties);

        self::assertSame($expected, $this->subject->getAlternative());
    }

    /**
     * @test
     */
    public function getPropertyReturnsProperty(): void
    {
        self::assertSame('foo.baz', $this->subject->getProperty('identifier'));
        self::assertSame('foo.baz', $this->subject->getProperty('name'));
        self::assertNull($this->subject->getProperty('alternative'));
        self::assertNull($this->subject->getProperty('title'));
    }

    /**
     * @test
     */
    public function hasPropertyTestsWhetherFileHasGivenProperty(): void
    {
        $this->initializeMetaData();

        self::assertTrue($this->subject->hasProperty('identifier'));
        self::assertTrue($this->subject->hasProperty('name'));
        self::assertTrue($this->subject->hasProperty('alternative'));
        self::assertTrue($this->subject->hasProperty('title'));
        self::assertFalse($this->subject->hasProperty('foo'));
        self::assertFalse($this->subject->hasProperty('baz'));
    }

    /**
     * @return \Generator<string, array{array<string, string>, string}>
     */
    public function getAlternativeReturnsAlternativeTextDataProvider(): \Generator
    {
        yield 'alternative only' => [['alternative' => 'alternative'], 'alternative'];
        yield 'title only' => [['title' => 'title'], 'title'];
        yield 'alternative and title' => [['alternative' => 'alternative', 'title' => 'title'], 'alternative'];
        yield 'no alternative and no title' => [[], 'foo'];
    }

    /**
     * @param array<string, mixed> $metaData
     */
    protected function initializeMetaData(array $metaData = []): void
    {
        $this->metaDataAspect = new MetaDataAspect($this->file);
        $this->metaDataAspect->add($metaData);

        GeneralUtility::addInstance(MetaDataAspect::class, $this->metaDataAspect);
    }
}
