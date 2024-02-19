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

namespace Cpsit\Typo3HandlebarsComponents\Tests\Unit;

use Cpsit\Typo3HandlebarsComponents\Exception\UnsupportedMethodException;
use Cpsit\Typo3HandlebarsComponents\Exception\UnsupportedTypeException;
use Cpsit\Typo3HandlebarsComponents\Tests\Unit\Fixtures\DynamicVariableInvocationTraitTestClass;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * DynamicVariableInvocationTraitTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class DynamicVariableInvocationTraitTest extends UnitTestCase
{
    protected DynamicVariableInvocationTraitTestClass $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new DynamicVariableInvocationTraitTestClass('foo');
    }

    /**
     * @test
     */
    public function staticCallThrowsExceptionIfMethodIsUnsupported(): void
    {
        $this->expectException(UnsupportedMethodException::class);
        $this->expectExceptionCode(1630334335);
        $this->expectExceptionMessage(sprintf('The method "foo" is not supported by the class "%s".', \get_class($this->subject)));

        /** @noinspection PhpUndefinedMethodInspection */
        /* @phpstan-ignore-next-line */
        $this->subject->foo();
    }

    /**
     * @test
     */
    public function staticCallThrowsExceptionIfGivenTypeIsNotSupported(): void
    {
        $constant = sprintf('%s::DUMMY', \get_class($this->subject));

        $this->expectException(UnsupportedTypeException::class);
        $this->expectExceptionCode(1630333499);
        $this->expectExceptionMessage(sprintf('The type "%s" is not supported.', $constant));

        /** @noinspection PhpUndefinedMethodInspection */
        /* @phpstan-ignore-next-line */
        $this->subject->isDummy();
    }

    /**
     * @test
     */
    public function staticCallReturnsStateOfGivenType(): void
    {
        self::assertTrue($this->subject->isFoo());
        self::assertFalse($this->subject->isBaz());
    }
}
